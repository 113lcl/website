<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'db.php';

if (!isset($_SESSION['user_id']) && isset($_COOKIE['rememberme'])) {
    $token = $_COOKIE['rememberme'];
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE remember_token = :token");
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
    }
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['logout'])) {
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE id = :id");
        $stmt->bindParam(':id', $_SESSION['user_id']);
        $stmt->execute();
    }
    setcookie('rememberme', '', time() - 3600, "/");
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

require_once __DIR__ . '/vendor/autoload.php';
use RobThree\Auth\TwoFactorAuth;

$userData = [
    'name' => 'User',
    'username' => 'username',
    'email' => 'user@example.com',
    'phone' => '',
    'created_at' => '2025'
];

try {
    // Подготовка запроса для получения данных пользователя
    $stmt = $pdo->prepare("SELECT name, username, email, phone, created_at, ga_secret FROM users WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $userData = $stmt->fetch(PDO::FETCH_ASSOC) ?: $userData;
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
}

$tfa = new TwoFactorAuth('MyWebsite');

// Включение 2FA по кнопке
if (isset($_POST['enable_2fa'])) {
    $secret = $tfa->createSecret();
    $stmt = $pdo->prepare("UPDATE users SET ga_secret = :secret WHERE id = :id");
    $stmt->bindParam(':secret', $secret);
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $userData['ga_secret'] = $secret;
}

// Показываем QR-код, если секрет есть
if (!empty($userData['ga_secret'])) {
    $qrCodeUrl = $tfa->getQRCodeImageAsDataUri($userData['username'], $userData['ga_secret']);
    $qrCodeHtml = '<img src="' . $qrCodeUrl . '" alt="QR для Google Authenticator">';
    $qrCodeHtml .= '<p>Сканируйте этот QR-код в Google Authenticator</p>';
} else {
    // Красивая кнопка
    $qrCodeHtml = '
    <form method="post" style="text-align:center; margin-top:20px;">
      <button name="enable_2fa" class="btn btn-2fa">
        <i class="bx bxs-shield"></i> Connect 2FA
      </button>
    </form>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Account</title>
  <link rel="stylesheet" href="css/account.css" >
  <script src="js/script.js" defer></script>
</head>
<body>
  <div class="wrapper">
    <form id="profileForm" action="update_profile.php" method="post" autocomplete="off">
      <div class="profile-header">
        <h1>
          Welcome,
          <span style="color:#fff;"><?= htmlspecialchars($userData['username']) ?></span>
          !
        </h1>
      </div>
      <div class="input-box">
        <span class="input-label">Name</span>
        <input class="edit-field" type="text" name="name" value="<?= htmlspecialchars($userData['name']) ?>" readonly required>
      </div>
      <div class="input-box readonly-box">
        <span class="input-label">Username</span>
        <input class="edit-field" type="text" name="username" value="<?= htmlspecialchars($userData['username']) ?>" readonly disabled required>
      </div>
      <div class="input-box">
        <span class="input-label">Email</span>
        <input class="edit-field" type="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>" readonly required>
      </div>
      <div class="input-box">
        <span class="input-label">Phone</span>
        <input class="edit-field" type="text" name="phone" value="<?= htmlspecialchars($userData['phone']) ?>" readonly required>
      </div>
      <div class="input-box readonly-box">
        <span class="input-label">Member since</span>
        <input class="edit-field" type="text" value="<?= htmlspecialchars($userData['created_at']) ?>" readonly disabled>
      </div>
      <div class="action-buttons">
        <button type="button" class="btn" id="editBtn" onclick="enableEdit()">Edit Profile</button>
        <button type="submit" class="btn" id="saveBtn" style="display:none;">Save</button>
        <button type="button" class="btn" id="cancelBtn" style="display:none;" onclick="cancelEdit()">Cancel</button>
        <button type="button" class="btn" onclick="togglePasswordForm()">Change Password</button>
      </div>
    </form>
    <form method="post" style="margin-top:10px;">
      <button type="submit" name="logout" class="btn logout">Logout</button>
    </form>
    <form class="password-form" id="passwordForm" action="change_password.php" method="post">
      <h3>Change Password</h3>
      <input type="password" name="old_password" placeholder="Current Password" required>
      <input type="password" name="new_password" placeholder="New Password" required>
      <button type="submit" class="btn">Update Password</button>
    </form>
    <div class="register-link">
      <p><a href="profile.php">Return</a></p>
    </div>
    <div class="two-factor-auth">
      <h3>Two-Factor Authentication</h3>
      <?= $qrCodeHtml ?>
    </div>
  </div>
  <script>
    let originalValues = {};
    function enableEdit() {
      document.querySelectorAll('.edit-field').forEach(function(input) {
        if (!input.disabled) {
          originalValues[input.name] = input.value;
          input.removeAttribute('readonly');
        }
      });
      // Скрыть поля только для чтения
      document.querySelectorAll('.readonly-box').forEach(function(box) {
        box.style.display = 'none';
      });
      document.getElementById('editBtn').style.display = 'none';
      document.getElementById('saveBtn').style.display = '';
      document.getElementById('cancelBtn').style.display = '';
    }
    function cancelEdit() {
      document.querySelectorAll('.edit-field').forEach(function(input) {
        if (originalValues[input.name] !== undefined) {
          input.value = originalValues[input.name];
          input.setAttribute('readonly', true);
        }
      });
      // Показать поля только для чтения обратно
      document.querySelectorAll('.readonly-box').forEach(function(box) {
        box.style.display = '';
      });
      document.getElementById('editBtn').style.display = '';
      document.getElementById('saveBtn').style.display = 'none';
      document.getElementById('cancelBtn').style.display = 'none';
    }
    function togglePasswordForm() {
      const pf = document.getElementById('passwordForm');
      pf.style.display = pf.style.display === 'block' ? 'none' : 'block';
    }
  </script>
</body>
</html>
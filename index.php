
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Name</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="wrapper">
        <header class="container">
            <span class="logo">Name of Company</span>
            <nav>
                <ul>
                    <li>
                        <a href="#">About us</a>
                        <ul class="dropdown">
                          <li><a href="#">Our Team</a></li>
                          <li><a href="#">Our History</a></li>
                        </ul>
                      </li>
                      <li>
                        <a href="#">Portfolio</a>
                        <ul class="dropdown">
                          <li><a href="#">Web</a></li>
                          <li><a href="#">Mobile</a></li>
                          <li><a href="#">Design</a></li>
                        </ul>
                      </li>
                      <li>
                        <a href="#">Services</a>
                        <ul class="dropdown">
                          <li><a href="#">Consulting</a></li>
                          <li><a href="#">Support</a></li>
                        </ul>
                      </li>
                    <li class="btn">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a id="accountButton" href="account.php">Account</a>
                        <?php else: ?>
                            <a id="accountButton" href="login.php">Login/Sign up</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </nav>
        </header>

        <div style="margin-top: 50px;" class="hero container">
            <div class="hero--info">
                <h2>Bank</h2>
                <h1>Work that we
                    produce for our
                    clients</h1>
                <p>Lorem Ipsum is simply dummy text of the printing and
                    typesetting industry. Lorem Ipsum has been the industry's
                    standard.</p>
                <button style="margin-bottom: 400px;" class="btn">Get more details</button>
            </div>
            <img src="img/picture4.svg" alt="">
        </div>
        <div class="container trending">
            <a href="cards.php" class="see-all">SEE ALL</a>
            <h3>Check this out</h3>
            

            <div class="cards">
                <img src="img/11.png" alt="Regular">
                <img src="img/12.png" alt="Junior">
                <img src="img/13.png" alt="Silver">
                <img src="img/14.png" alt="Business">
            </div>              
        </div>
        <div class="container big-text">
          <p style="margin-bottom: 100px;" >Lorem Ipsum is simply dummy text of the printing and typesetting industry. 
            Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, 
            when an unknown printer took a galley of type and scrambled it to make a type 
            specimen book.</p>
        </div>
      
    </div>
    <script src="js/script.js"></script>
    <a href="support.php" class="floating-button">💬</a>
    <footer class="footer">
      <div class="container">
          <div class="footer-content">
              <p>&copy; 2025 Name of Company. Все права защищены.</p>
              <p>Лицензия №123456 | <a style="color: white;" href="privacy.php">Политика конфиденциальности</a> | <a style="color: white;" href="terms.php">Условия использования</a></p>
          </div>
          <div class="footer-links">
              <a href="#">О нас</a>
              <a style="margin-left: 30px;" href="#">Контакты</a>
              <a style="margin-left: 30px;" href="#">Помощь</a>
          </div>
          <div class="social-links">
              <a href="#"><i class='bx bxl-facebook'></i></a>
              <a href="#"><i class='bx bxl-twitter'></i></a>
              <a href="#"><i class='bx bxl-linkedin'></i></a>
          </div>
      </div>
  </footer>
  
</body>

</html>
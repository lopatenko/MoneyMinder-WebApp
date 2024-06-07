<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Minder - Your Personal Banking Assistant</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.7.6/lottie.min.js"></script>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            transition: all 0.3s ease;
        }
        .header {
            background-color: #f0f0f0;
            color: #333;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1001;
        }
        .header h1 {
            font-size: 28px;
            margin: 0;
            flex-grow: 1;
            text-align: center;
        }
        .widget {
            display: flex;
            align-items: center;
            min-width: 100px;
            flex-shrink: 0;
        }
        .widget p {
            margin: 0;
            font-size: 14px;
        }
        .widget img {
            width: 24px;
            height: 24px;
            margin-right: 5px;
        }
        .widget.time {
            margin-left: auto;
            padding-right: 20px;
        }
        .widget.currency {
            margin-right: auto;
            flex-direction: column;
            text-align: right;
            padding-left: 20px;
        }
        .widget.currency p {
            display: flex;
            align-items: center;
            padding-left: 30px;
        }
        .header button {
            padding: 10px;
            border-radius: 50%;
            border: none;
            background-color: transparent;
            color: #333;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 24px;
            margin-left: 15px;
        }
        .header button:hover {
            background-color: rgba(0, 0, 0, 0.1);
        }
        .main-content {
            display: flex;
            padding: 20px;
            transition: all 0.3s ease;
            flex-wrap: wrap;
            margin-top: 80px; /* Отступ для учета фиксированного заголовка */
        }
        .left-sidebar,
        .main-content-area {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .left-sidebar {
            width: 250px;
            background-color: #f0f0f0;
            color: #333;
            padding: 20px;
            position: fixed;
            top: 80px; /* Расположение под заголовком */
            left: 0;
            height: calc(100% - 80px); /* Учитываем высоту заголовка */
            z-index: 1000;
            transition: left 0.3s ease;
        }
        .left-sidebar.closed {
            left: -250px;
        }
        .left-sidebar h3 {
            margin-top: 0;
            font-size: 22px;
            margin-bottom: 20px;
        }
        .left-sidebar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .left-sidebar li {
            margin-bottom: 10px;
        }
        .left-sidebar a {
            color: #333;
            text-decoration: none;
            display: block;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            padding: 10px;
        }
        .left-sidebar a:hover {
            background-color: #cccccc;
        }
        .main-content-area {
            flex-grow: 1;
            background-color: #ffffff;
            padding: 30px;
            margin-left: 270px;
            min-width: 300px;
            transition: margin-left 0.3s ease;
        }
        .main-content-area.expanded {
            margin-left: 270px;
        }
        .main-content-area.collapsed {
            margin-left: 20px;
        }
        .main-content-area h2 {
            margin-top: 0;
            font-size: 26px;
            margin-bottom: 20px;
        }
        .banner {
            background-color: #f0f0f0;
            color: #000000 ;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 10px;
        }
        .features {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .feature {
            background-color: #f0f0f0;
            padding: 20px;
            flex: 1;
            margin: 10px;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.3s;
        }
        .feature img {
            width: 50px;
            height: 50px;
        }
        .feature:hover {
            transform: translateY(-10px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }
        .testimonials {
            background-color: #f0f0f0;
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
        }
        .testimonial {
            margin-bottom: 10px;
        }
        .testimonial p {
            margin: 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border-radius: 5px;
            border: 1px solid #d0d0d0;
            background-color: #e0e0e0;
            color: #333;
        }
        .form-group button {
            padding: 10px 20px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #45a049;
        }
        .map {
            margin: 20px 0;
            text-align: center;
        }
        .map iframe {
            width: 100%;
            height: 300px;
            border: 0;
            border-radius: 10px;
        }
        .footer {
            background-color: #e0e0e0;
            color: #333;
            padding: 20px;
            text-align: center;
            position: relative;
            bottom: 0;
            width: 100%;
            box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.1);
        }
        .footer p {
            margin: 0;
            font-size: 16px;
        }
        .footer .social-icons {
            margin-top: 10px;
        }
        .footer .social-icons a {
            color: #333;
            text-decoration: none;
            margin: 0 10px;
            font-size: 20px;
            transition: color 0.3s;
        }
        .footer .social-icons a:hover {
            color: #4caf50;
        }
        .menu-toggle {
            display: block;
            position: absolute;
            top: 20px;
            left: 20px;
            cursor: pointer;
            z-index: 1002;
        }
        .menu-toggle span {
            display: block;
            width: 30px;
            height: 5px;
            background-color: #333;
            margin-bottom: 5px;
            border-radius: 5px;
        }
        @media (max-width: 768px) {
            .header h1 {
                position: static;
                transform: none;
                text-align: center;
                width: 100%;
                order: 2;
            }
            .header button {
                order: 3;
                margin-left: 0;
                margin-top: 10px;
            }
            .header .widget {
                order: 1;
                margin-left: 0;
                margin-right: 0;
            }
            .main-content-area {
                margin-left: 0;
                margin-top: 20px;
            }
            .menu-toggle {
                top: 20px;
            }
            body {
                transition: all 0.3s ease;
            }
            .left-sidebar {
                left: -250px;
            }
            .left-sidebar.open {
                left: 0;
            }
            .main-content-area.expanded {
                margin-left: 270px;
            }
            .main-content-area.collapsed {
                margin-left: 0;
            }
        }
        .faq-section {
            background-color: #f0f0f0;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .faq-item {
            margin-bottom: 10px;
        }
        .faq-question {
            background-color: #4caf50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            width: 100%;
            text-align: left;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .faq-question:hover {
            background-color: #45a049;
        }
        .faq-answer {
            display: none;
            padding: 10px;
            background-color: #e0e0e0;
            border-radius: 5px;
            margin-top: 5px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #2c2c2c;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            color: #ecf0f1;
            border-radius: 10px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }
        .gallery {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 20px;
        }
        .gallery img {
            max-width: 200px;
            margin: 10px;
            border-radius: 10px;
            transition: transform 0.3s;
        }
        .gallery img:hover {
            transform: scale(1.1);
        }
        .gallery-title {
            text-align: center;
        }
        .slider {
            position: relative;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            overflow: hidden;
            border-radius: 15px;
        }
        .slides {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }
        .slide {
            min-width: 100%;
            box-sizing: border-box;
        }
        .slide img {
            width: 100%;
            border-radius: 15px;
        }
        .prev,
        .next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: #fff;
            border: none;
            font-size: 24px;
            padding: 10px;
            cursor: pointer;
        }
        .prev {
            left: 10px;
        }
        .next {
            right: 10px;
        }
        .loading-screen {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(0, 0, 0, 0.8);
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1000;
        }
        .loading-icon {
            font-size: 48px;
        }
        .header .avatar {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin-right: 20px; /* Добавлено для перемещения влево */
        }
        .header .avatar img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-left: 10px;
        }
        .header .avatar .username {
            font-size: 16px;
        }
        .download-button {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            background-color: #4caf50;
            color: white;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            margin-left: auto;
            margin-right: auto;
            display: block;
        }
        .download-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<div class="loading-screen">
    <div class="loading-icon">
        <i class="fas fa-spinner fa-spin"></i>
    </div>
</div>
<div class="header">
    <div class="widget currency">
        <p>
            <img src="images/dollar.png" alt="Dollar Icon">
            USD to UAH: <span id="usdRate"></span>
        </p>
        <p>
            <img src="images/euro.png" alt="Euro Icon">
            EUR to UAH: <span id="eurRate"></span>
        </p>
    </div>
    <img src="images/logo_with_text_3.png" width="8%" height="6%" alt="Logo Money Minder with Text">
    <div class="widget time">
        <img src="images/clock.png" alt="Clock Icon">
        <p id="currentTime"></p>
    </div>
    <div class="avatar">
        <span class="username"><?php echo htmlspecialchars($_SESSION['Name']); ?></span>
        <img src="images/avatar_pig.png" alt="User Avatar">
    </div>
    <div class="menu-toggle" onclick="toggleMenu()">
        <span></span>
        <span></span>
        <span></span>
    </div>
</div>
<div class="main-content">
    <div class="left-sidebar" id="leftSidebar">
        <h3>Navigation</h3>
        <ul class="menu">
            <li><a href="index.php">Home</a></li>
            <li><a href="income.php">Income</a></li>
            <li><a href="expenses.php">Expenses</a></li>
            <li><a href="settings.php">Settings</a></li>
        </ul>
    </div>
    <div class="main-content-area expanded" id="mainContentArea">
        <div class="banner animate__animated animate__fadeInDown">
            <h2>Welcome to Money Minder</h2>
            <p>Manage your finances effortlessly with our intuitive and secure platform.</p>
        </div>
        <div class="features">
            <div class="feature animate__animated animate__fadeInUp" data-aos="fade-up">
                <img src="images/lock.png" alt="Secure Transactions">
                <h3>Secure Transactions</h3>
                <p>All your transactions are encrypted and secure.</p>
            </div>
            <div class="feature animate__animated animate__fadeInUp" data-aos="fade-up">
                <img src="images/money.png" alt="Easy Budgeting">
                <h3>Easy Budgeting</h3>
                <p>Set up budgets and track your spending with ease.</p>
            </div>
            <div class="feature animate__animated animate__fadeInUp" data-aos="fade-up">
                <img src="images/bell.png" alt="Real-time Notifications">
                <h3>Real-time Notifications</h3>
                <p>Receive instant alerts for all your account activities.</p>
            </div>
            <div class="feature animate__animated animate__fadeInUp" data-aos="fade-up">
                <img src="images/flag.png" alt="Comprehensive Reports">
                <h3>Comprehensive Reports</h3>
                <p>Get detailed reports on your financial health.</p>
            </div>
        </div>
        <div class="testimonials" data-aos="fade-up">
            <h3>What Our Customers Say</h3>
            <div class="testimonial">
                <p>"Money Minder has transformed the way I manage my finances. Highly recommended!"</p>
                <p>- Vladimir Lopatenko</p>
            </div>
            <div class="testimonial">
                <p>"thatz rely vary good ap i recoment it vary mach"</p>
                <p>- Denis Slipko</p>
            </div>
        </div>
        <div class="faq-section" data-aos="fade-up">
            <h2>FAQ</h2>
            <div class="faq-item">
                <button class="faq-question">What is Money Minder?</button>
                <div class="faq-answer">
                    <p>Money Minder is your personal banking assistant to manage finances efficiently.</p>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-question">How secure is my data?</button>
                <div class="faq-answer">
                    <p>All transactions are encrypted and securely stored.</p>
                </div>
            </div>
        </div>
        <h2 class="gallery-title">Our Mobile App</h2>
        <div class="gallery" data-aos="fade-up">
            <img src="images/mobile_app_login.png" alt="Image 1">
            <img src="images/mobile_app_main.png" alt="Image 2">
            <img src="images/mobile_app_1.png" alt="Image 3">
            <img src="images/mobile_app_2.png" alt="Image 4">
        </div>
        <a href="https://github.com/holerrrr/MoneyMinder.git" class="download-button">Download Our Mobile App</a>
        <div class="map" data-aos="fade-up">
            <h3>Nearest branches of the bank</h3>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3169.953082799846!2d-122.084249684691!3d37.42199977982509!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808fb5bcb631e9f1%3A0x50f1a2e44a6a546!2sGoogleplex!5e0!3m2!1sen!2sus!4v1580125920370!5m2!1sen!2sus" allowfullscreen></iframe>
        </div>
    </div>
</div>
<div class="footer">
    <p>&copy; 2024 Money Minder. All rights reserved.</p>
    <div class="social-icons">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
    </div>
</div>
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Some text in the Modal..</p>
    </div>
</div>
<div id="lottieIcon"></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Loading screen
        setTimeout(() => {
            document.querySelector('.loading-screen').style.display = 'none';
        }, 1000);

        updateTimeAndRates();

        AOS.init();

        var modal = document.getElementById("myModal");
        var span = document.getElementsByClassName("close")[0];

        span.onclick = function() {
            modal.style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        document.querySelector('.faq-section').addEventListener('click', function(e) {
            if (e.target.classList.contains('faq-question')) {
                var answer = e.target.nextElementSibling;
                answer.style.display = answer.style.display === 'block' ? 'none' : 'block';
            }
        });

        var animation = lottie.loadAnimation({
            container: document.getElementById('lottieIcon'),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: 'path/to/your/lottie/animation.json' // путь к вашему JSON файлу с анимацией
        });

        var currentSlide = 0;
        function moveSlide(step) {
            var slides = document.querySelector('.slides');
            var slidesCount = slides.children.length;
            currentSlide = (currentSlide + step + slidesCount) % slidesCount;
            slides.style.transform = 'translateX(-' + (currentSlide * 100) + '%)';
        }
        document.querySelector('.prev').onclick = function() { moveSlide(-1); }
        document.querySelector('.next').onclick = function() { moveSlide(1); }
    });

    function toggleMenu() {
        var leftSidebar = document.getElementById("leftSidebar");
        var mainContentArea = document.getElementById("mainContentArea");
        leftSidebar.classList.toggle('closed');
        if (leftSidebar.classList.contains('closed')) {
            mainContentArea.classList.remove('expanded');
            mainContentArea.classList.add('collapsed');
        } else {
            mainContentArea.classList.add('expanded');
            mainContentArea.classList.remove('collapsed');
        }
    }

    function updateTimeAndRates() {
        var currentTimeElement = document.getElementById("currentTime");
        setInterval(() => {
            var now = new Date();
            currentTimeElement.innerText = now.toLocaleTimeString();
        }, 1000);

        fetch('https://api.exchangerate-api.com/v4/latest/USD')
            .then(response => response.json())
            .then(data => {
                document.getElementById("usdRate").innerText = (data.rates.UAH).toFixed(2);
                document.getElementById("eurRate").innerText = (data.rates.UAH / data.rates.EUR).toFixed(2);
            })
            .catch(error => console.error('Error fetching exchange rates:', error));
    }
</script>
</body>
</html>


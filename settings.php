<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "root", "moneyminder");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];

    // Check if the provided email and old password are correct
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $oldPassword);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update the password
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $newPassword, $email);
        if ($stmt->execute()) {
            $message = 'Password updated successfully!';
        } else {
            $message = 'Error updating password. Please try again.';
        }
    } else {
        $message = 'Incorrect email or password.';
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Minder - Settings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="css/settingsStyles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
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
        <span class="username">
            <?php echo htmlspecialchars($_SESSION['Name']); ?>
        </span>
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
        <div class="settings-section animate__animated animate__fadeInUp" data-aos="fade-up">
            <h2>Change Password</h2>
            <form id="settingsForm" method="POST" action="">
                <input type="email" id="email" name="email" placeholder="Email" required>
                <input type="password" id="oldPassword" name="oldPassword" placeholder="Old Password" required>
                <input type="password" id="newPassword" name="newPassword" placeholder="New Password" required>
                <button type="submit">Update Password</button>
            </form>
            <?php if ($message): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
            <button type="button" id="logoutButton" class="logout-btn">Logout</button>

        </div>
        <div class="password-tips animate__animated animate__fadeInUp" data-aos="fade-up">
            <h2>Password Tips</h2>
            <ul>
                <li>Use a mix of letters, numbers, and special characters.</li>
                <li>Avoid using easily guessable information like your name or birthdate.</li>
                <li>Change your passwords regularly.</li>
                <li>Do not share your password with anyone.</li>
                <li>Use different passwords for different accounts.</li>
            </ul>
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
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Loading screen
        setTimeout(() => {
            document.querySelector('.loading-screen').style.display = 'none';
        }, 1000);

        updateTimeAndRates();

        AOS.init();
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

    document.getElementById('logoutButton').addEventListener('click', function() {
        fetch('logout.php')
            .then(() => {
                window.location.href = 'login.php';
            })
            .catch(error => console.error('Logout failed:', error));
    });

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


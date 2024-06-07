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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'addIncome') {
    $incomeAmount = $_POST['incomeAmount'];
    $userId = $_SESSION['user_id'];

    // Insert the new income into the database
    $stmt = $conn->prepare("INSERT INTO income (User_id, Sum) VALUES (?, ?)");
    $stmt->bind_param("id", $userId, $incomeAmount);

    if ($stmt->execute()) {
        // Get the ID of the newly inserted income
        $incomeId = $stmt->insert_id;

        // Update user balance
        $stmt = $conn->prepare("UPDATE balance SET balance = balance + ? WHERE User_id = ?");
        $stmt->bind_param("di", $incomeAmount, $userId);
        $stmt->execute();

        // Fetch updated balance
        $balanceResult = $conn->query("SELECT balance FROM balance WHERE User_id = '$userId'");
        $balanceRow = $balanceResult->fetch_assoc();
        $userBalance = $balanceRow['balance'];

        // Return response
        echo json_encode([
            'success' => true,
            'id' => $incomeId,
            'balance' => number_format($userBalance, 2)
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding income.']);
    }

    exit;
}

// Fetch user balance
$userId = $_SESSION['user_id'];
$balanceResult = $conn->query("SELECT balance FROM balance WHERE User_id = '$userId'");
if ($balanceResult->num_rows > 0) {
    $balanceRow = $balanceResult->fetch_assoc();
    $userBalance = $balanceRow['balance'];
} else {
    $userBalance = 0;
}

// Fetch latest incomes without sorting by date
$incomesResult = $conn->query("SELECT Income_ID, Sum FROM income WHERE User_id = '$userId'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Minder - Manage Your Income</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="css/incomeStyles.css">
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
        <div class="balance-card animate__animated animate__fadeInUp" data-aos="fade-up">
            <h2>Current Balance</h2>
            <p id="userBalance"><?php echo number_format($userBalance, 2); ?> UAH</p>
        </div>
        <div class="content-wrapper" style="display: flex; flex-wrap: wrap; justify-content: space-between;">
            <div class="income-form animate__animated animate__fadeInUp" data-aos="fade-up">
                <h2>Add New Income</h2>
                <form id="incomeForm">
                    <input type="number" id="incomeAmount" name="incomeAmount" placeholder="Amount" step="0.01" required>
                    <button type="submit">Add Income</button>
                </form>
            </div>
            <div class="income-list animate__animated animate__fadeInDown" data-aos="fade-down">
                <h2>Income List</h2>
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody id="incomeTableBody">
                    <!-- Income entries will be added here dynamically -->
                    <?php
                    if ($incomesResult->num_rows > 0) {
                        while($row = $incomesResult->fetch_assoc()) {
                            echo "<tr><td>" . $row["Income_ID"] . "</td><td>" . $row["Sum"] . "</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No income found</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tips animate__animated animate__fadeInLeft" data-aos="fade-left">
            <h2>Financial Tips</h2>
            <ul>
                <li>Track your expenses regularly to avoid overspending.</li>
                <li>Create a monthly budget and stick to it.</li>
                <li>Set financial goals and work towards achieving them.</li>
                <li>Save a portion of your income for emergencies.</li>
                <li>Invest wisely to grow your wealth.</li>
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

        var form = document.getElementById('incomeForm');
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            var incomeAmount = document.getElementById('incomeAmount').value;

            var formData = new FormData();
            formData.append('action', 'addIncome');
            formData.append('incomeAmount', incomeAmount);

            fetch('', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        var incomeTableBody = document.getElementById('incomeTableBody');
                        var newRow = incomeTableBody.insertRow();
                        newRow.insertCell(0).innerText = data.id;
                        newRow.insertCell(1).innerText = incomeAmount;

                        document.getElementById('incomeForm').reset();
                        alert('Income Added: ' + data.id + ' - ' + incomeAmount);

                        // Update balance
                        var userBalanceElement = document.getElementById('userBalance');
                        userBalanceElement.innerText = data.balance + ' UAH';
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        });

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

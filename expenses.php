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

// Fetch user balance
$userId = $_SESSION['user_id'];
$balanceResult = $conn->query("SELECT balance FROM balance WHERE User_id = '$userId'");
if ($balanceResult->num_rows > 0) {
    $balanceRow = $balanceResult->fetch_assoc();
    $userBalance = $balanceRow['balance'];
} else {
    // If no balance record exists for the user, initialize balance to 0
    $userBalance = 0;
}

// Handle AJAX request to add expense
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addExpense') {
    // Retrieve form data
    $expenseName = $_POST['expenseName'];
    $expenseAmount = $_POST['expenseAmount'];
    $expenseCategory = $_POST['expenseCategory'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert expense data into the database
        $sql = "INSERT INTO expenses (User_id, ID_Category, description, Sum) VALUES ('$userId', '$expenseCategory', '$expenseName', '$expenseAmount')";
        if ($conn->query($sql) !== TRUE) {
            throw new Exception("Error: " . $sql . "<br>" . $conn->error);
        }

        // Update user balance
        $newBalance = $userBalance - $expenseAmount;
        $sql = "UPDATE balance SET balance = '$newBalance' WHERE User_id = '$userId'";
        if ($conn->query($sql) !== TRUE) {
            throw new Exception("Error: " . $sql . "<br>" . $conn->error);
        }

        // Commit transaction
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'New expense added successfully and balance updated.']);
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Fetch expense data for chart
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getExpenseData') {
    $sql = "SELECT c.Name as category, SUM(e.Sum) as total FROM expenses e JOIN categories c ON e.ID_Category = c.ID_Category WHERE e.User_id = '$userId' GROUP BY e.ID_Category";
    $result = $conn->query($sql);
    $data = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    echo json_encode($data);
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Minder - Manage Your Expenses</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="css/expensesStyles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.7.6/lottie.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
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
        <div class="charts-expense-container">
            <div class="chart-container animate__animated animate__fadeInRight" data-aos="fade-right">
                <h2>Spending Chart</h2>
                <canvas id="spendingChart"></canvas>
            </div>
            <div class="expense-form animate__animated animate__fadeInUp" data-aos="fade-up">
                <h2>Add New Expense</h2>
                <form id="expenseForm">
                    <input type="text" id="expenseName" name="expenseName" placeholder="Expense Name" required>
                    <input type="number" id="expenseAmount" name="expenseAmount" placeholder="Amount" step="0.01" required>
                    <select id="expenseCategory" name="expenseCategory" required>
                        <option value="">Select Category</option>
                        <?php
                        // Fetch categories from the database
                        $sql = "SELECT ID_Category, Name FROM categories";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row["ID_Category"] . "'>" . $row["Name"] . "</option>";
                            }
                        }
                        ?>
                    </select>
                    <button type="submit">Add Expense</button>
                </form>
            </div>
        </div>
        <div class="expense-list animate__animated animate__fadeInDown" data-aos="fade-down">
            <h2>Expense List</h2>
            <select id="searchCategory">
                <option value="">All Categories</option>
                <option value="Transport">Transport</option>
                <option value="Sport">Sport</option>
                <option value="Food">Food</option>
                <option value="Restaurants">Restaurants</option>
                <option value="Clothing">Clothing</option>
                <option value="Other">Other</option>
            </select>
            <table>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Amount</th>
                    <th>Category</th>
                </tr>
                </thead>
                <tbody id="expenseTableBody">
                <!-- Expenses will be added here dynamically -->
                <?php
                // Fetch expenses from the database for the logged-in user
                $sql = "SELECT e.description, e.Sum, c.Name FROM expenses e JOIN categories c ON e.ID_Category = c.ID_Category WHERE e.User_id = '$userId'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr><td>" . $row["description"]. "</td><td>" . $row["Sum"]. "</td><td>" . $row["Name"]. "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No expenses found</td></tr>";
                }
                ?>
                </tbody>
            </table>
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
        updateTimeAndRates();
        loadExpenseData();

        var form = document.getElementById('expenseForm');
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            var expenseName = document.getElementById('expenseName').value;
            var expenseAmount = document.getElementById('expenseAmount').value;
            var expenseCategory = document.getElementById('expenseCategory').value;

            var formData = new FormData();
            formData.append('action', 'addExpense');
            formData.append('expenseName', expenseName);
            formData.append('expenseAmount', expenseAmount);
            formData.append('expenseCategory', expenseCategory);

            fetch('expenses.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        var expenseTableBody = document.getElementById('expenseTableBody');
                        var newRow = expenseTableBody.insertRow();
                        newRow.insertCell(0).innerText = expenseName;
                        newRow.insertCell(1).innerText = expenseAmount;
                        newRow.insertCell(2).innerText = expenseCategory;

                        document.getElementById('expenseForm').reset();
                        alert('Expense Added: ' + expenseName + ' - ' + expenseAmount + ' - ' + expenseCategory);
                        loadExpenseData();  // Reload chart data after adding a new expense
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        });

        var searchCategorySelect = document.getElementById('searchCategory');
        searchCategorySelect.addEventListener('change', function() {
            var filter = searchCategorySelect.value;
            var expenseTableBody = document.getElementById('expenseTableBody');
            var rows = expenseTableBody.getElementsByTagName('tr');
            for (var i = 0; i < rows.length; i++) {
                var categoryCell = rows[i].getElementsByTagName('td')[2];
                if (categoryCell) {
                    var categoryText = categoryCell.textContent || categoryCell.innerText;
                    if (filter === '' || categoryText === filter) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }
        });

        AOS.init();
    });

    function loadExpenseData() {
        fetch('expenses.php?action=getExpenseData')
            .then(response => response.json())
            .then(data => {
                var labels = data.map(item => item.category);
                var values = data.map(item => parseFloat(item.total));

                var ctx = document.getElementById('spendingChart').getContext('2d');
                var spendingChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Expenses',
                            data: values,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.label || '';
                                        var value = context.raw || 0;
                                        return label + ': ' + value + ' UAH';
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching expense data:', error));
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
</script>
</body>
</html>

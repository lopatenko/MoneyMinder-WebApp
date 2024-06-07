<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Подключение к базе данных
$conn = new mysqli("localhost", "root", "root", "moneyminder");

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Получение данных о пользователе из POST-запроса
$name = isset($_POST['username']) ? $_POST['username'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : null;

// Debugging: Check received values
var_dump($name, $email, $password, $confirm_password);

if (!$name || !$email || !$password || !$confirm_password) {
    echo "All fields are required";
    exit();
}

if ($password !== $confirm_password) {
    echo "Passwords do not match";
    exit();
}

// Проверка уникальности электронной почты
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Электронная почта уже зарегистрирована
    echo "Email already registered";
} else {
    // Регистрация нового пользователя без хеширования пароля
    $stmt = $conn->prepare("INSERT INTO users (Name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);
    if ($stmt->execute()) {
        // Пользователь успешно зарегистрирован
        echo "Registration successful";

        // Получаем ID только что созданного пользователя
        $user_id = $stmt->insert_id;

        // Заполнение таблиц начальными значениями для нового пользователя
        // Начальные значения для expenses
        $stmt = $conn->prepare("INSERT INTO expenses (User_id, ID_Category, description, Sum) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisd", $user_id, $category_id, $description, $amount);

        // Здесь вы можете добавить начальные траты для пользователя
        // Пример:
        $categories = array(1, 2, 3, 4, 5, 6); // Здесь ID категорий, которые у вас есть
        foreach ($categories as $category_id) {
            $description = "Initial expense";
            $amount = 0.0; // Начальная сумма трат
            $stmt->execute();
        }

        // Начальные значения для income
        $stmt = $conn->prepare("INSERT INTO income (User_id, Sum) VALUES (?, ?)");
        $stmt->bind_param("id", $user_id, $income);

        $income = 0; // Начальный доход
        $stmt->execute();

        // Получаем ID только что созданной записи о доходе
        $income_id = $stmt->insert_id;

        // Начальные значения для balance
        $balance = 0.0; // Начальный баланс
        $stmt = $conn->prepare("INSERT INTO balance (balance, User_id, Income_ID) VALUES (?, ?, ?)");
        $stmt->bind_param("dii", $balance, $user_id, $income_id);

        $stmt->execute();
    } else {
        // Ошибка регистрации
        echo "Registration failed: " . $conn->error;
    }
}

header("Location: ../login.php");
exit();

$stmt->close();
$conn->close();
?>

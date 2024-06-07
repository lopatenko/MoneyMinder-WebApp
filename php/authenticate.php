<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Подключение к базе данных
$conn = new mysqli("localhost", "root", "root", "moneyminder");

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo "Invalid request method. Expected POST method.";
    exit();
}

// Получение данных о пользователе из POST-запроса
$email = isset($_POST['email']) ? $_POST['email'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;

if (!$email || !$password) {
    echo "All fields are required";
    exit();
}

// Проверка учетных данных пользователя
$stmt = $conn->prepare("SELECT User_id, Name, password FROM users WHERE email = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();

    // Проверка пароля (без хеширования)
    if ($password === $user['password']) {
        // Успешный вход
        $_SESSION['user_id'] = $user['User_id'];
        $_SESSION['Name'] = $user['Name'];

        // Debug: Print session variables
        var_dump($_SESSION);

        // Перенаправление на защищенную страницу или домашнюю страницу
        header("Location: ../index.php");
        exit();
    } else {
        // Неправильный пароль
        echo "Invalid password";
    }
} else {
    // Электронная почта не найдена
    echo "Email not registered";
}

$stmt->close();
$conn->close();
?>

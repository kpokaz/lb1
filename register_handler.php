<?php
session_start();

// Параметры подключения
$host = 'db'; // имя контейнера с MySQL из docker-compose.yml
$dbname = 'lb_pdo_goods';
$username = 'user';
$password = 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
} catch (PDOException $e) {
    die("DB error: " . $e->getMessage());
}

// Отримання та валідація
$user = $_POST['username'] ?? '';
$pass = $_POST['password'] ?? '';

if (strlen($user) < 3 || strlen($pass) < 4) {
    $_SESSION['reg_error'] = 'Логін або пароль занадто короткий';
    header('Location: register.php');
    exit;
}

// Перевірка унікальності
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$user]);

if ($stmt->rowCount() > 0) {
    $_SESSION['reg_error'] = 'Такий логін вже існує';
    header('Location: register.php');
    exit;
}

// Запис в БД
$hash = password_hash($pass, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
$stmt->execute([$user, $hash]);

$_SESSION['logged_in'] = true;
header('Location: index.html');
exit;
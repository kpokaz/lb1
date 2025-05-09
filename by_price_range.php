<?php

header("Content-Type: application/javascript; charset=utf-8");

$host = 'db';
$dbname = 'lb_pdo_goods';
$username = 'user';
$password = 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $_GET['callback'] . '({"error": "Ошибка подключения: ' . $e->getMessage() . '"});';
    exit;
}

$min = isset($_GET['min']) ? (float)$_GET['min'] : 0;
$max = isset($_GET['max']) ? (float)$_GET['max'] : 100000;

$sql = "SELECT items.name, items.price, items.quantity, items.quality, vendors.v_name, category.c_name 
        FROM items 
        INNER JOIN vendors ON items.FID_Vendor = vendors.ID_Vendors 
        INNER JOIN category ON items.FID_Category = category.ID_Category
        WHERE items.price BETWEEN :min AND :max";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':min', $min);
$stmt->bindValue(':max', $max);
$stmt->execute();

$results = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $results[] = $row;
}

// Передача даних як результат виклику callback
echo $_GET['callback'] . '(' . json_encode($results, JSON_UNESCAPED_UNICODE) . ');';
?>
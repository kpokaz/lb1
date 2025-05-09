<?php

header("Content-Type: text/html; charset=utf-8");

$host = 'db';
$dbname = 'lb_pdo_goods';
$username = 'user';
$password = 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
    exit;
}

if (!isset($_GET['categories']) || !is_array($_GET['categories'])) {
    echo "Не переданы категории";
    exit;
}

$placeholders = implode(',', array_map(fn($key) => ":category$key", array_keys($_GET['categories'])));
$sql = "SELECT items.name, items.price, items.quantity, items.quality, vendors.v_name, category.c_name 
        FROM items 
        INNER JOIN vendors ON items.FID_Vendor = vendors.ID_Vendors 
        INNER JOIN category ON items.FID_Category = category.ID_Category
        WHERE category.c_name IN ($placeholders)";
$stmt = $pdo->prepare($sql);

foreach ($_GET['categories'] as $key => $category) {
    $stmt->bindValue(":category$key", $category);
}

$stmt->execute();

$output = "<h2>Product sort by category:</h2><table border='1'>
<tr><th>Name</th><th>Price</th><th>Quantity</th><th>Quality</th><th>Vendor</th><th>Category</th></tr>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $output .= "<tr>
        <td>" . htmlspecialchars($row['name']) . "</td>
        <td>" . htmlspecialchars($row['price']) . "</td>
        <td>" . htmlspecialchars($row['quantity']) . "</td>
        <td>" . htmlspecialchars($row['quality']) . "</td>
        <td>" . htmlspecialchars($row['v_name']) . "</td>
        <td>" . htmlspecialchars($row['c_name']) . "</td>
    </tr>";
}
$output .= "</table>";
echo $output;
?>
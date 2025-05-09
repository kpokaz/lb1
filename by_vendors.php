<?php

header("Content-Type: text/xml; charset=utf-8");

$host = 'db';
$dbname = 'lb_pdo_goods';
$username = 'user';
$password = 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "<?xml version='1.0' encoding='UTF-8'?><error>Ошибка подключения: " . $e->getMessage() . "</error>";
    exit;
}

if (!isset($_GET['vendors']) || !is_array($_GET['vendors'])) {
    echo "<?xml version='1.0' encoding='UTF-8'?><error>Не переданы вендоры</error>";
    exit;
}

$placeholders = implode(',', array_map(fn($key) => ":vendor$key", array_keys($_GET['vendors'])));
$sql = "SELECT items.name, items.price, items.quantity, items.quality, vendors.v_name 
        FROM items 
        INNER JOIN vendors ON items.FID_Vendor = vendors.ID_Vendors 
        WHERE vendors.v_name IN ($placeholders)";
$stmt = $pdo->prepare($sql);

foreach ($_GET['vendors'] as $key => $vendor) {
    $stmt->bindValue(":vendor$key", $vendor);
}

$stmt->execute();

echo "<?xml version='1.0' encoding='UTF-8'?><items>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<item>";
    echo "<name>" . htmlspecialchars($row['name']) . "</name>";
    echo "<price>" . htmlspecialchars($row['price']) . "</price>";
    echo "<quantity>" . htmlspecialchars($row['quantity']) . "</quantity>";
    echo "<quality>" . htmlspecialchars($row['quality']) . "</quality>";
    echo "<vendor>" . htmlspecialchars($row['v_name']) . "</vendor>";
    echo "</item>";
}

echo "</items>";
?>
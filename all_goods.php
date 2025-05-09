<?php
$host = 'db';
$dbname = 'lb_pdo_goods';
$username = 'user';
$password = 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<p style='color: red;'>Ошибка подключения: " . $e->getMessage() . "</p>");
}

$sql = "SELECT items.name, items.price, items.quantity, items.quality, vendors.v_name, category.c_name 
        FROM items 
        INNER JOIN vendors ON items.FID_Vendor = vendors.ID_Vendors 
        INNER JOIN category ON items.FID_Category = category.ID_Category";

$stmt = $pdo->query($sql);

$output_all = "<h2>All information about all products:</h2>";
$output_all .= "<table border='1'>
                <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Quality</th>
                    <th>Vendor</th>
                    <th>Category</th>
                </tr>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $output_all .= "<tr>";
    $output_all .= "<td>" . htmlspecialchars($row['name']) . "</td>";
    $output_all .= "<td>" . htmlspecialchars($row['price']) . "</td>";
    $output_all .= "<td>" . htmlspecialchars($row['quantity']) . "</td>";
    $output_all .= "<td>" . htmlspecialchars($row['quality']) . "</td>";
    $output_all .= "<td>" . htmlspecialchars($row['v_name']) . "</td>";
    $output_all .= "<td>" . htmlspecialchars($row['c_name']) . "</td>";
    $output_all .= "</tr>";
}
$output_all .= "</table>";

echo $output_all;
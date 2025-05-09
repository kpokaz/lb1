<?php
// Подключение к базе данных
$host = 'db';
$dbname = 'lb_pdo_goods';
$username = 'user';
$password = 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
} catch (PDOException $e) {
    file_put_contents('error_log.txt', "DB connection failed: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'DB connection failed']);
    exit;
}

// Получаем JSON из тела POST-запроса
$data = json_decode(file_get_contents("php://input"), true);

// Логируем входящие данные
file_put_contents('log.txt', "Received data: " . print_r($data, true) . "\n", FILE_APPEND);

// Проверка на наличие всех нужных полей
if (
    isset($data['time'], $data['userAgent'], $data['latitude'], $data['longitude'], $data['action'], $data['username'])
) {
    try {
        // Преобразование ISO8601 времени в формат MySQL DATETIME
        $datetime = date('Y-m-d H:i:s', strtotime($data['time']));

        $stmt = $pdo->prepare("
            INSERT INTO request_logs (time, user_agent, latitude, longitude, action, username) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $datetime,
            $data['userAgent'],
            $data['latitude'],
            $data['longitude'],
            $data['action'],
            $data['username']
        ]);

        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        file_put_contents('error_log.txt', "SQL Error: " . $e->getMessage() . "\n", FILE_APPEND);
        echo json_encode(['status' => 'error', 'message' => 'Failed to insert data']);
    }
} else {
    file_put_contents('error_log.txt', "Missing required fields: " . print_r($data, true) . "\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => 'Missing required data']);
}
?>
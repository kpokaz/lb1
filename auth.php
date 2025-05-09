<?php
session_start();

// Подключение к базе
$host = 'db';
$dbname = 'lb_pdo_goods';
$username = 'user';
$password = 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
} catch (PDOException $e) {
    http_response_code(500);
    echo "DB connection failed";
    exit;
}

// Если пришёл POST-запрос — обрабатываем логин
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$input_username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($input_password, $user['password_hash'])) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $input_username;
        ?>
        <!DOCTYPE html>
        <html lang="uk">
        <head>
            <meta charset="UTF-8">
            <title>Вхід успішний</title>
        </head>
        <body>
            <h2>Ви увійшли як <?= htmlspecialchars($input_username) ?></h2>
            <p>Логування події входу...</p>

            <script>
                function sendLog(logData) {
                    fetch('log.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(logData)
                    }).then(res => res.json())
                      .then(data => console.log('Log result:', data))
                      .catch(err => console.error('Log error:', err));
                }

                function logLoginEvent() {
                    const timestamp = new Date().toISOString();
                    const userAgent = navigator.userAgent;
                    const username = <?= json_encode($input_username) ?>;

                    if (!navigator.geolocation) {
                        sendLog({ time: timestamp, userAgent, latitude: null, longitude: null, action: 'login_success', username });
                    } else {
                        navigator.geolocation.getCurrentPosition(
                            pos => {
                                sendLog({
                                    time: timestamp,
                                    userAgent,
                                    latitude: pos.coords.latitude,
                                    longitude: pos.coords.longitude,
                                    action: 'login_success',
                                    username
                                });
                            },
                            err => {
                                sendLog({ time: timestamp, userAgent, latitude: null, longitude: null, action: 'login_success', username });
                            }
                        );
                    }
                }

                window.addEventListener('DOMContentLoaded', logLoginEvent);
            </script>
        </body>
        </html>
        <?php
        exit;
    } else {
        echo "<p>Невірний логін або пароль. <a href='login.php'>Спробуйте знову</a></p>";
        exit;
    }
}
?>
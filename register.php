<?php
session_start();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Реєстрація</title>
</head>
<body>
    <h2>Реєстрація</h2>
    <?php if (isset($_SESSION['reg_error'])): ?>
        <p style="color:red;"><?= $_SESSION['reg_error'] ?></p>
        <?php unset($_SESSION['reg_error']); ?>
    <?php endif; ?>
    <form action="register_handler.php" method="post">
        <label>Логін: <input type="text" name="username" required></label><br><br>
        <label>Пароль: <input type="password" name="password" required></label><br><br>
        <input type="submit" value="Зареєструватися">
    </form>
    <p>Уже маєш акаунт? <a href="login.php">Увійти</a></p>
</body>
</html>
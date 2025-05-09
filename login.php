<?php session_start(); ?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Вхід до системи логів</title>
</head>
<body>
    <h2>Вхід</h2>
    
    <!-- Отображение ошибок сессии -->
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color:red;"><?= $_SESSION['error'] ?></p>
        <?php unset($_SESSION['error']); ?>  <!-- Удаление ошибки после отображения -->
    <?php endif; ?>

    <form action="auth.php" method="POST">
        <label>Логін: <input type="text" name="username" required></label><br><br>
        <label>Пароль: <input type="password" name="password" required></label><br><br>
        <input type="submit" value="Увійти">
    </form>
</body>
</html>
<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['username'] === 'admin' && $_POST['password'] === '12345') {
        $_SESSION['is_logged_in'] = true;
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = 'Адміністратор';
        header('Location: index.php');
        exit;
    } else {
        $error = "Невірний логін або пароль!";
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вхід</title>
</head>
<body>

    <h2>Авторизація</h2>
    <form action="" method="POST">
        <div>
            <label for="username">Ім'я користувача:</label>
            <input type="text" name="username" id="username" required>
        </div>
        <br>
        <div>
            <label for="password">Пароль:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <br>
        <input type="submit" value="Увійти">
    </form>

</body>
</html>
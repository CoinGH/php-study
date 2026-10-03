<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    echo "<pre>";
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    $priority = $_POST['priority'];

    if (empty($title)):$errors[] = "Поле Назва є обов'язковим для заповнення!"; endif;
    if (empty($description)):$errors[] = "Поле Опис є обов'язковим для заповнення!"; endif;

    var_dump($title, $description, $priority);
    echo "</pre>";
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Додати у Список Справ</title>
    <style>
        .alert {
            color: red;
        }
    </style>
</head>
<body>
    <div class="alert">
        <ul>
            <?php if (!empty($errors)): foreach($errors as $error):?>
            <li class="error_li">
                <?= $error ?>
            </li>
            <?php endforeach; endif; ?>
        </ul>
    </div>
    <form action="create.php" method="POST">
        <input type="text" name="title">
        <textarea name="description"></textarea>
        <select name="priority">
            <option value="low">Низький</option>
            <option value="medium">Середній</option>
            <option value="high">Високий</option>
            <option value="ultra">Ультра</option>
            <option value="maximum">Максимальний</option>
        </select>
        <button type="submit">Зберегти!</button>
    </form>
    <a href="index.php">Повернутись до списку</a>
</body>
</html>

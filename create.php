<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<pre>";
    var_dump($_POST);
    echo "</pre>";
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Додати у Список Справ</title>
</head>
<body>
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

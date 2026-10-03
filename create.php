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
            text-decoration: none;
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
        <input type="text" name="title" value="<?php if (!empty($errors)):?><?= $title ?? "" ?><?php endif; ?>">
        <textarea name="description"><?php if (!empty($errors)):?><?= $description ?? "" ?><?php endif; ?></textarea>
        <select name="priority">
            <option value="low" <?= ($priority ?? 'low') === 'low' ? 'selected' : '' ?> >Низький</option>
            <option value="medium" <?= ($priority ?? 'medium') === 'medium' ? 'selected' : '' ?> >Середній</option>
            <option value="high" <?= ($priority ?? 'high') === 'high' ? 'selected' : '' ?> >Високий</option>
            <option value="ultra" <?= ($priority ?? 'ultra') === 'ultra' ? 'selected' : '' ?> >Ультра</option>
            <option value="maximum" <?= ($priority ?? 'maximum') === 'maximum' ? 'selected' : '' ?> >Максимальний</option>
        </select>
        <button type="submit">Зберегти!</button>
    </form>
    <a href="index.php">Повернутись до списку</a>
</body>
</html>

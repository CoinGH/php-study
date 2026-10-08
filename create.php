<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $extensions = ['jpg', 'jpeg', 'png'];

    echo "<pre>";
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    $priority = $_POST['priority'];
    $filename = $_FILES['avatar']['name'];
    $need_to_upload = true;

    if (empty($title)): $errors[] = "Поле Назва є обов'язковим для заповнення!"; $need_to_upload = false; endif;
    if (empty($description)): $errors[] = "Поле Опис є обов'язковим для заповнення!"; $need_to_upload = false; endif;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['avatar']['size'] <= 3 * 1024 * 1024) {
            if (in_array(pathinfo($filename, PATHINFO_EXTENSION), $extensions, true)) {
                if ($need_to_upload) {
                    move_uploaded_file($_FILES['avatar']['tmp_name'], 'uploads/' . uniqid() . '_' . $_FILES['avatar']['name']);
                }
            } else {
                $errors[] = "Неприпустиме розширення файлу!";
            }
        } else {
            $errors[] = "Файл завеликий!";
        }
    };

    var_dump($title, $description, $priority);
    var_dump($_FILES);
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
    <form action="create.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="title" value="<?php if (!empty($errors)):?><?= $title ?? "" ?><?php endif; ?>">
        <textarea name="description"><?php if (!empty($errors)):?><?= $description ?? "" ?><?php endif; ?></textarea>
        <select name="priority">
            <option value="low" <?= ($priority ?? 'low') === 'low' ? 'selected' : '' ?> >Низький</option>
            <option value="medium" <?= ($priority ?? 'medium') === 'medium' ? 'selected' : '' ?> >Середній</option>
            <option value="high" <?= ($priority ?? 'high') === 'high' ? 'selected' : '' ?> >Високий</option>
            <option value="ultra" <?= ($priority ?? 'ultra') === 'ultra' ? 'selected' : '' ?> >Ультра</option>
            <option value="maximum" <?= ($priority ?? 'maximum') === 'maximum' ? 'selected' : '' ?> >Максимальний</option>
        </select>
        <input type="file" name="avatar" accept="image/png, image/jpeg">
        <button type="submit">Зберегти!</button>
    </form>
    <a href="index.php">Повернутись до списку</a>
</body>
</html>

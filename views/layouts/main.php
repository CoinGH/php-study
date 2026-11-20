<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Task Manager') ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <h1>Task Manager (MVC)</h1>
        <nav>
            <?php if (!empty($_SESSION['user_id'])): ?>
                Привіт, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Користувач') ?>!
                <a href="/logout">[Вийти]</a> |
                <a href="/">Мої завдання</a> |
                <a href="/tasks/create">Створити завдання</a>
            <?php else: ?>
                <a href="/login">Увійти</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <?= $content ?>
    </main>
    <footer>
        <p>&copy; <?= date('Y') ?> Task Manager</p>
    </footer>
</body>
</html>

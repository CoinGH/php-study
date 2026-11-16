<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/Task.php';
require_once __DIR__ . '/TaskRepository.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$repo = new TaskRepository(__DIR__ . '/tasks.json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $title    = trim($_POST['title'] ?? '');
        $deadline = trim($_POST['deadline'] ?? '') ?: null;
        if (!empty($title)) {
            $repo->add($title, $deadline);
        }
    }

    if ($action === 'complete') {
        $repo->complete((int) ($_POST['id'] ?? 0));
    }

    if ($action === 'delete') {
        $repo->delete((int) ($_POST['id'] ?? 0));
    }

    header('Location: index.php');
    exit;
}

$tasks = $repo->findAll();

$appName = "Task Manager";
date_default_timezone_set('Europe/Kiev');

function formatTitle($text, $maxLength = 20) {
    if (mb_strlen($text) > $maxLength) {
        return mb_substr($text, 0, $maxLength) . '...';
    }
    return $text;
}

function getCurrentGreeting() {
    $currentTime = date('H');
    $result = "Доброї ночі";
    if (6 <= $currentTime && $currentTime < 12) {
        $result = "Доброго ранку";
    } elseif (12 <= $currentTime && $currentTime < 18) {
        $result = "Добрий день";
    } elseif (18 <= $currentTime && $currentTime < 24) {
        $result = "Добрий вечір";
    }
    return $result;
}

?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $appName ?></title>
    <style>
        .task-done {
            color: green;
            -webkit-text-stroke: 0.1px black;
        }
        .task-pending {
            color: gray;
        }
    </style>
</head>
<body>
    <header>
        <h1>
            <?php if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true): ?>
                Привіт, <?= htmlspecialchars($_SESSION['user_name']) ?>! <a href="logout.php">[Вийти]</a>
            <?php else: ?>
                Ви увійшли як Гість. <a href="login.php">[Увійти]</a>
            <?php endif; ?>
        </h1>
        <h1><?= $appName ?></h1>
        <h2><?= getCurrentGreeting() ?>!</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <input type="text" name="title" placeholder="Назва завдання" required>
            <input type="date" name="deadline">
            <button type="submit">Додати</button>
        </form>
    </header>
    <main>
    <div class="stats">
        Всього: <?= count($tasks) ?> |
        Виконано: <?= $repo->countCompleted() ?> |
        Прострочено: <?= $repo->countOverdue() ?>
    </div>
    <ul>
        <?php foreach ($tasks as $task): ?>
            <li class="<?= $task->isCompleted() ? 'task-done' : 'task-pending' ?>">
                <?= htmlspecialchars(formatTitle($task->getTitle())) ?>
                <?= $task->getDeadline() ? '(' . htmlspecialchars($task->getDeadline()) . ')' : '' ?>
                — <?= $task->getStatusLabel() ?>

                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="complete">
                    <input type="hidden" name="id" value="<?= $task->getId() ?>">
                    <button type="submit">✓</button>
                </form>

                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $task->getId() ?>">
                    <button type="submit">✕</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
    </main>
</body>
</html>
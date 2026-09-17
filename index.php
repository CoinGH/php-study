<?php

$appName = "Task Manager";
$taskTimeEstimate = 2;
$isCompleted = true;
$taskTitle = "Actually I hate niggle!";
date_default_timezone_set('Europe/Kyiv');

function formatTitle($text, $maxLength = 20) {
    if (strlen($text) > $maxLength) {
        return substr($text, 0, $maxLength) . '...';
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
        <h1><?= $appName ?></h1>
        <h2><?= getCurrentGreeting() ?>!</h2>
        <h2></h2>
    </header>
    <main>
        <ul>
            <li class="<?= $isCompleted ? 'task-done' : 'task-pending' ?>">
                <?= formatTitle($taskTitle) ?>
                <?php if ($isCompleted): ?>
                    ✔️ Виконано
                <?php else: ?>
                    🕒 В процесі
                <?php endif; ?>
            </li>
            <li>Орієнтовний час: <?= $taskTimeEstimate ?> год.</li>
        </ul>
    </main>
</body>
</html>
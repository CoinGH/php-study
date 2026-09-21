<?php

$appName = "Task Manager";
date_default_timezone_set('Europe/Kyiv');

$tasks = [
    [
        'id' => 0,
        'title' => "Play Games",
        'priority' => 'Ultra High',
        'is_completed' => false
    ],
    [
        'id' => 1,
        'title' => "Do homework",
        'priority' => 'Low',
        'is_completed' => false
    ],
    [
        'id' => 2,
        'title' => "Procrastinate",
        'priority' => 'Maximum',
        'is_completed' => true
    ],
    [
        'id' => 3,
        'title' => "Calculate 1000 - 800",
        'priority' => 'Medium',
        'is_completed' => true
    ],
        [
        'id' => 4,
        'title' => "Vibecode",
        'priority' => 'High',
        'is_completed' => false
    ],
];

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
        .brdr {
            border: 1px solid black;
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
            <?php foreach ($tasks as $task): ?>
            <li class="<?= $task['is_completed'] ? 'task-done' : 'task-pending' ?>"> 
                <?= formatTitle($task['title']) ?> 
                <?php if ($task['is_completed']): ?>
                        ✔️ Виконано
                    <?php else: ?>
                        🕒 В процесі
                    <?php endif; ?>
            </li>
            <li>Пріорітет: <?= $task['priority'] ?>.</li>
            <?php endforeach; ?>
        </ul>
    </main>
</body>
</html>
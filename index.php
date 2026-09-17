<?php

$appName = "Task Manager";
$taskTitle = "Оголошення змінних PHP";
$taskTimeEstimate = 2;

$isCompleted = true;

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
            outline: 1px;
            outline-color: black;
        }
        .task-pending {
            color: gray;
        }
    </style>
</head>
<body>
    <header>
        <h1><?= $appName ?></h1>
    </header>
    <main>
        <ul>
            <li><?= $taskTitle ?></li>
            <li><?= $taskTimeEstimate ?></li>
        </ul>
        <p class="<?= $isCompleted ? 'task-done' : 'task-pending' ?>"><?php if ($isCompleted): ?>✔️ Виконано<?php else: ?>🕒 В процесі<?php endif; ?></p>
    </main>
</body>
</html>

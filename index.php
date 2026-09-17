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
    </header>
    <main>
        <ul>
            <li class="<?= $isCompleted ? 'task-done' : 'task-pending' ?>">
                <?= $taskTitle ?> 
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
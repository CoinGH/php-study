<?php
declare(strict_types=1);

$host = '127.0.0.1';
$port = '3306';
$dbname = 'task_manager';
$username = 'root';
$password = '';
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

$tables = [];
$tasks = [];
$connectionStatus = false;
$errorMessage = null;
$serverInfo = '';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
    $serverInfo = (string) $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);

    $tablesStmt = $pdo->query('SHOW TABLES');
    $tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);

    if (in_array('tasks', $tables, true)) {
        $tasksStmt = $pdo->query('SELECT * FROM tasks ORDER BY id ASC');
        $tasks = $tasksStmt->fetchAll();
    }

    $connectionStatus = true;
} catch (PDOException $e) {
    $connectionStatus = false;
    $errorMessage = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Діагностика БД</title>
    <style>
        .alert-error {
            background: #ffe3e3;
            border: 1px solid #ffa8a8;
            color: #c92a2a;
            padding: 10px;
        }
        .alert-success {
            background: #d3f9d8;
            border: 1px solid #8ce99a;
            color: #2b8a3e;
            padding: 10px;
        }
        .task-done {
            color: green;
        }
        .task-pending {
            color: gray;
        }
        table {
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #f1f3f5;
        }
        .priority-high { color: #c92a2a; }
        .priority-medium { color: #a66b00; }
        .priority-low { color: #1971c2; }
    </style>
</head>
<body>
    <h1>Діагностика підключення до БД MySQL</h1>
    <p>База даних: <?= htmlspecialchars($dbname) ?></p>

    <?php if ($connectionStatus): ?>
        <div class="alert-success">
            Успішне підключення через PDO!
            <br>Версія MySQL: <?= htmlspecialchars($serverInfo) ?>
        </div>

        <h2>Таблиці у базі даних (SHOW TABLES)</h2>
        <?php if (!empty($tables)): ?>
            <ul>
                <?php foreach ($tables as $table): ?>
                    <li><?= htmlspecialchars($table) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>У базі даних немає таблиць.</p>
        <?php endif; ?>

        <h2>Тестові записи (SELECT * FROM tasks)</h2>
        <?php if (!empty($tasks)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Назва завдання</th>
                        <th>User ID</th>
                        <th>Пріоритет</th>
                        <th>Опис</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <?php
                        $priorityClass = match ($task['priority']) {
                            'High' => 'priority-high',
                            'Low' => 'priority-low',
                            default => 'priority-medium',
                        };
                        ?>
                        <tr>
                            <td><?= (int) $task['id'] ?></td>
                            <td><?= htmlspecialchars($task['title']) ?></td>
                            <td><?= (int) $task['user_id'] ?></td>
                            <td class="<?= $priorityClass ?>"><?= htmlspecialchars($task['priority']) ?></td>
                            <td><?= htmlspecialchars($task['description']) ?></td>
                            <td class="<?= $task['is_completed'] ? 'task-done' : 'task-pending' ?>">
                                <?= $task['is_completed'] ? 'Виконано' : 'В процесі' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>У таблиці tasks немає записів або таблиця ще не створена.</p>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert-error">
            Помилка підключення до СКБД!
            <br><?= htmlspecialchars($errorMessage ?? 'Невідома помилка') ?>
        </div>
    <?php endif; ?>

    <a href="index.php">Повернутись до списку</a>
</body>
</html>

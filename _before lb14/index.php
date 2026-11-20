<?php
declare(strict_types=1);
session_start();
date_default_timezone_set('Europe/Kiev');

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Task.php';
require_once __DIR__ . '/TaskStorageInterface.php';
require_once __DIR__ . '/TaskRepository.php';
require_once __DIR__ . '/Exceptions.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$currentUserId = (int) $_SESSION['user_id'];
try {
    $repo = new TaskRepository(Database::getConnection());
} catch (RuntimeException $e) {
    http_response_code(503);
    exit('Помилка підключення до бази даних.');
}

$formErrors = [];
$feedbackMessage = $_SESSION['feedback'] ?? null;
unset($_SESSION['feedback']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'add') {
            $title    = $_POST['title'] ?? '';
            $deadline = trim($_POST['deadline'] ?? '') ?: null;

            $repo->add($currentUserId, $title, $deadline);
            $_SESSION['feedback'] = 'Завдання успішно збережено в базі даних!';
            header('Location: index.php');
            exit;
        }

        if ($action === 'complete') {
            $repo->complete((int) ($_POST['id'] ?? 0), $currentUserId);
            $_SESSION['feedback'] = 'Статус завдання оновлено!';
            header('Location: index.php');
            exit;
        }

        if ($action === 'delete') {
            $repo->delete((int) ($_POST['id'] ?? 0), $currentUserId);
            $_SESSION['feedback'] = 'Завдання видалено з бази даних!';
            header('Location: index.php');
            exit;
        }
    } catch (ValidationException $e) {
        $formErrors = $e->getErrors();
    } catch (TaskNotFoundException $e) {
        $formErrors = [$e->getMessage()];
    } catch (TaskManagerException $e) {
        $formErrors = ['Помилка програми: ' . $e->getMessage()];
    } catch (Throwable $e) {
        $formErrors = ['Критичний збій обробки запиту. Зверніться до техпідтримки.'];
    }
}

$perPage = 5;
$page = max(1, (int) ($_GET['page'] ?? 1));
try {
    $totalTasks = $repo->countByUser($currentUserId);
    $totalPages = max(1, (int) ceil($totalTasks / $perPage));
    $page = min($page, $totalPages);
    $tasks = $repo->findAllByUser($currentUserId, $page, $perPage);
    $completedCount = $repo->countCompletedByUser($currentUserId);
    $overdueCount = $repo->countOverdueByUser($currentUserId);
} catch (PDOException $e) {
    http_response_code(503);
    exit('Помилка отримання завдань з бази даних.');
}

$appName = "Task Manager";

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
        .pagination {
            margin-top: 20px;
        }
        .pagination a, .pagination strong {
            padding: 5px;
        }
        .task-done {
            color: green;
            -webkit-text-stroke: 0.1px black;
        }
        .task-pending {
            color: gray;
        }
        .task-meta {
            font-size: 0.85em;
            color: #868e96;
        }
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
        <h1><?= $appName ?> (MySQL + PDO)</h1>
        <p>ID користувача: <?= $currentUserId ?></p>
        <h2><?= getCurrentGreeting() ?>!</h2>
        <a href="test_db.php">Діагностика БД (MySQL)</a>

        <?php if ($feedbackMessage): ?>
            <div class="alert-success"><?= htmlspecialchars($feedbackMessage) ?></div>
        <?php endif; ?>

        <?php if (!empty($formErrors)): ?>
            <div class="alert-error">
                <strong>Увага! Виявлено помилки під час виконання операції:</strong>
                <ul>
                    <?php foreach ($formErrors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="action" value="add">
            <input type="text" name="title" placeholder="Назва завдання (мінімум 3 символи)" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
            <input type="date" name="deadline" value="<?= htmlspecialchars($_POST['deadline'] ?? '') ?>">
            <button type="submit">Додати</button>
        </form>
    </header>
    <main>
    <div class="stats">
        Всього: <?= $totalTasks ?> |
        Виконано: <?= $completedCount ?> |
        Прострочено: <?= $overdueCount ?>
    </div>
    <h2>Список завдань (Сторінка <?= $page ?> з <?= $totalPages ?>)</h2>
    <ul>
        <?php foreach ($tasks as $task): ?>
            <li class="<?= $task->isCompleted() ? 'task-done' : 'task-pending' ?>">
                #<?= $task->getId() ?> <?= htmlspecialchars($task->getTitle()) ?>
                <?= $task->getDeadline() ? '(' . htmlspecialchars($task->getDeadline()) . ')' : '' ?>
                — <?= $task->getStatusLabel() ?>

                <?php if (!$task->isCompleted()): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="complete">
                        <input type="hidden" name="id" value="<?= $task->getId() ?>">
                        <button type="submit">✓</button>
                    </form>
                <?php endif; ?>

                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $task->getId() ?>">
                    <button type="submit">✕</button>
                </form>

                <div class="task-meta">
                    Створено: <?= htmlspecialchars($task->getCreatedAt()) ?>
                    <?php if ($task->getUpdatedAt()): ?>
                        | Оновлено: <?= htmlspecialchars($task->getUpdatedAt()) ?>
                    <?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>">« Назад</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i === $page): ?>
                    <strong><?= $i ?></strong>
                <?php else: ?>
                    <a href="?page=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>">Вперед »</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    </main>
</body>
</html>

<?php
declare(strict_types=1);
session_start();
date_default_timezone_set('Europe/Kiev');

require_once __DIR__ . '/Task.php';
require_once __DIR__ . '/TaskStorageInterface.php';
require_once __DIR__ . '/TaskRepository.php';
require_once __DIR__ . '/Exceptions.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Залежність оголошена через інтерфейс
/** @var TaskStorageInterface $repo */
$repo = new TaskRepository(__DIR__ . '/tasks.json');

$formErrors = [];
$feedbackMessage = $_SESSION['feedback'] ?? null;
unset($_SESSION['feedback']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'add') {
            $title    = $_POST['title'] ?? '';
            $deadline = trim($_POST['deadline'] ?? '') ?: null;

            $repo->add($title, $deadline);
            $_SESSION['feedback'] = 'Завдання успішно додано!';
            header('Location: index.php');
            exit;
        }

        if ($action === 'complete') {
            $repo->complete((int) ($_POST['id'] ?? 0));
            $_SESSION['feedback'] = 'Статус завдання оновлено!';
            header('Location: index.php');
            exit;
        }

        if ($action === 'delete') {
            $repo->delete((int) ($_POST['id'] ?? 0));
            $_SESSION['feedback'] = 'Завдання видалено!';
            header('Location: index.php');
            exit;
        }
    } catch (ValidationException $e) {
        // Перехоплюємо помилки валідації форми
        $formErrors = $e->getErrors();
    } catch (TaskNotFoundException $e) {
        // Перехоплюємо спробу маніпуляції неіснуючим завданням
        $formErrors = [$e->getMessage()];
    } catch (TaskManagerException $e) {
        // Загальний доменний виняток
        $formErrors = ['Помилка програми: ' . $e->getMessage()];
    } catch (Throwable $e) {
        // Непередбачена системна помилка
        $formErrors = ['Критичний збій обробки запиту. Зверніться до техпідтримки.'];
    } finally {
        // Код у finally виконується незалежно від наявності чи відсутності винятку
        // Наприклад, запис системного аудиту або закриття додаткових ресурсів
    }
}

$tasks = $repo->findAll();

$appName = "Task Manager";

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
        <h1><?= $appName ?></h1>
        <h2><?= getCurrentGreeting() ?>!</h2>

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
    </main>
</body>
</html>

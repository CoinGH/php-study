<h2>Список моїх завдань (Сторінка <?= $page ?> з <?= $totalPages ?>)</h2>
<p>ID користувача: <?= (int) $_SESSION['user_id'] ?></p>
<p>Всього: <?= $totalTasks ?> | Виконано: <?= $completedCount ?> | Прострочено: <?= $overdueCount ?></p>

<?php if ($feedbackMessage): ?>
    <div class="alert-success"><?= htmlspecialchars($feedbackMessage) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<p><a href="/tasks/create">+ Нове завдання</a></p>

<?php if (empty($tasks)): ?>
    <p>У вас ще немає створених завдань.</p>
<?php else: ?>
    <ul>
        <?php foreach ($tasks as $task): ?>
            <li class="<?= $task->isCompleted() ? 'task-done' : 'task-pending' ?>">
                #<?= $task->getId() ?> <?= htmlspecialchars($task->getTitle()) ?>
                <?= $task->getDeadline() ? '(' . htmlspecialchars($task->getDeadline()) . ')' : '' ?>
                — <?= $task->getStatusLabel() ?>
                <form method="POST" action="/tasks/complete" class="inline">
                    <input type="hidden" name="id" value="<?= $task->getId() ?>">
                    <button type="submit"><?= $task->isCompleted() ? '↩ Відновити' : '✓ Завершити' ?></button>
                </form>
                <form method="POST" action="/tasks/delete" class="inline">
                    <input type="hidden" name="id" value="<?= $task->getId() ?>">
                    <button type="submit" onclick="return confirm('Видалити завдання?')">✕</button>
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
<?php endif; ?>

<?php if ($totalPages > 1): ?>
    <nav class="pagination">
        <?php if ($page > 1): ?>
            <a href="/?page=<?= $page - 1 ?>">« Назад</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i === $page): ?>
                <strong><?= $i ?></strong>
            <?php else: ?>
                <a href="/?page=<?= $i ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
            <a href="/?page=<?= $page + 1 ?>">Вперед »</a>
        <?php endif; ?>
    </nav>
<?php endif; ?>

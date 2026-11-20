<h2>Створення нового завдання</h2>

<?php if (!empty($errors)): ?>
    <div class="alert-error">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="/tasks/create">
    <div class="form-group">
        <label for="title">Назва завдання:</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($old['title'] ?? '') ?>" placeholder="Назва завдання" autofocus>
    </div>
    <div class="form-group">
        <label for="deadline">Дедлайн:</label>
        <input type="date" id="deadline" name="deadline" value="<?= htmlspecialchars($old['deadline'] ?? '') ?>">
    </div>
    <button type="submit">Зберегти завдання</button>
    <a href="/">Скасувати</a>
</form>

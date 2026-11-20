<h2>Авторизація</h2>

<?php if (!empty($error)): ?>
    <div class="alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" action="/login">
    <div class="form-group">
        <label for="username">Ім'я користувача:</label>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($oldUsername ?? '') ?>" required>
    </div>
    <div class="form-group">
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit">Увійти</button>
</form>

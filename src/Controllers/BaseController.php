<?php
declare(strict_types=1);

abstract class BaseController
{
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = __DIR__ . '/../../views/' . $view . '.php';
        $layoutFile = __DIR__ . '/../../views/layouts/main.php';

        if (!is_file($viewFile) || !is_file($layoutFile)) {
            throw new RuntimeException('Файл представлення або макета не знайдено.');
        }

        ob_start();
        try {
            require $viewFile;
            $content = ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }
        require $layoutFile;
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function requireAuth(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
    }
}

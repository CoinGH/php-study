<?php
declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    $paths = ['src/', 'src/Controllers/', 'src/Models/', 'src/Repositories/', 'src/Exceptions/'];
    foreach ($paths as $path) {
        $file = __DIR__ . '/../' . $path . $class . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

session_start();
date_default_timezone_set('Europe/Kyiv');

$router = new Router();
$router->get('/', [TaskController::class, 'index']);
$router->get('/tasks/create', [TaskController::class, 'create']);
$router->post('/tasks/create', [TaskController::class, 'store']);
$router->post('/tasks/complete', [TaskController::class, 'complete']);
$router->post('/tasks/delete', [TaskController::class, 'delete']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (Throwable $e) {
    http_response_code(500);
    error_log((string) $e);
    echo 'Помилка роботи застосунку. Перевірте підключення до бази даних.';
}

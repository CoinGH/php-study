<?php
declare(strict_types=1);

class Router
{
    private array $routes = [];

    public function get(string $uri, array $action): void
    {
        $this->routes['GET'][$uri] = $action;
    }

    public function post(string $uri, array $action): void
    {
        $this->routes['POST'][$uri] = $action;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/') ?: '/';
        $method = strtoupper($method);

        if (!isset($this->routes[$method][$path])) {
            $this->handleNotFound($method, $path);
            return;
        }

        [$controllerClass, $actionMethod] = $this->routes[$method][$path];
        if (!class_exists($controllerClass)) {
            throw new RuntimeException('Клас контролера не знайдено.');
        }
        $controller = new $controllerClass();
        if (!method_exists($controller, $actionMethod)) {
            throw new RuntimeException('Метод контролера не знайдено.');
        }
        $controller->$actionMethod();
    }

    private function handleNotFound(string $method, string $path): void
    {
        http_response_code(404);
        ?>
        <!DOCTYPE html>
        <html lang="uk">
        <head>
            <meta charset="UTF-8">
            <title>404 — Сторінку не знайдено</title>
            <link rel="stylesheet" href="/assets/css/style.css">
        </head>
        <body>
            <main class="not-found">
                <h1>404</h1>
                <p>Маршрут <code><?= htmlspecialchars($method) ?> <?= htmlspecialchars($path) ?></code> не зареєстровано в системі.</p>
                <a href="/">Повернутися на головну</a>
            </main>
        </body>
        </html>
        <?php
    }
}

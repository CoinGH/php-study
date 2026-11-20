<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/Task.php';
require_once __DIR__ . '/TaskStorageInterface.php';

class TaskRepository implements TaskStorageInterface
{
    public function __construct(private PDO $pdo) {}

    public function findAllByUser(int $userId, int $page = 1, int $perPage = 5): array
    {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE user_id = :user_id ORDER BY id DESC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(fn(array $row) => Task::fromArray($row), $stmt->fetchAll());
    }

    public function countByUser(int $userId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM tasks WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    public function countCompletedByUser(int $userId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM tasks WHERE user_id = :user_id AND is_completed = 1');
        $stmt->execute(['user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    public function countOverdueByUser(int $userId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM tasks WHERE user_id = :user_id AND is_completed = 0 AND deadline IS NOT NULL AND deadline < NOW()');
        $stmt->execute(['user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    public function findByIdAndUser(int $id, int $userId): ?Task
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        $row = $stmt->fetch();
        return $row ? Task::fromArray($row) : null;
    }

    public function add(int $userId, string $title, ?string $deadline = null): Task
    {
        $errors = [];
        $title = trim($title);
        if ($title === '') {
            $errors[] = 'Назва завдання є обов\'язковою для заповнення.';
        } elseif (mb_strlen($title) < 3) {
            $errors[] = 'Назва завдання повинна містити щонайменше 3 символи.';
        } elseif (mb_strlen($title) > 255) {
            $errors[] = 'Назва завдання не може містити більше 255 символів.';
        }

        $deadline = $deadline !== null ? trim($deadline) : '';
        if ($deadline !== '') {
            $date = DateTimeImmutable::createFromFormat('!Y-m-d', $deadline);
            if (!$date || $date->format('Y-m-d') !== $deadline) {
                $errors[] = 'Вказано некоректну дату дедлайну.';
            }
        } else {
            $deadline = null;
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        $stmt = $this->pdo->prepare('INSERT INTO tasks (user_id, title, description, priority, is_completed, deadline, created_at) VALUES (:user_id, :title, :description, :priority, 0, :deadline, NOW())');
        $stmt->execute([
            'user_id' => $userId,
            'title' => $title,
            'description' => '',
            'priority' => 'Medium',
            'deadline' => $deadline,
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $task = $this->findByIdAndUser($id, $userId);
        if ($task === null) {
            throw new RuntimeException('Не вдалося отримати створене завдання.');
        }
        return $task;
    }

    public function complete(int $id, int $userId): bool
    {
        $stmt = $this->pdo->prepare('UPDATE tasks SET is_completed = 1 - is_completed, updated_at = NOW() WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $id, 'user_id' => $userId]);

        if ($stmt->rowCount() === 0 && $this->findByIdAndUser($id, $userId) === null) {
            throw new TaskNotFoundException($id);
        }
        return true;
    }

    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $id, 'user_id' => $userId]);

        if ($stmt->rowCount() === 0) {
            throw new TaskNotFoundException($id);
        }
        return true;
    }
}

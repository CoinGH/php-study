<?php
declare(strict_types=1);

class TaskController extends BaseController
{
    private TaskStorageInterface $repository;

    public function __construct()
    {
        $this->requireAuth();
        $this->repository = new TaskRepository(Database::getConnection());
    }

    public function index(): void
    {
        $userId = (int) $_SESSION['user_id'];
        $perPage = 5;
        $totalTasks = $this->repository->countByUser($userId);
        $totalPages = max(1, (int) ceil($totalTasks / $perPage));
        $page = min($totalPages, max(1, (int) ($_GET['page'] ?? 1)));
        $tasks = $this->repository->findAllByUser($userId, $page, $perPage);
        $feedbackMessage = $_SESSION['feedback'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['feedback'], $_SESSION['error']);

        $this->render('tasks/index', [
            'pageTitle' => 'Мої завдання',
            'tasks' => $tasks,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalTasks' => $totalTasks,
            'completedCount' => $this->repository->countCompletedByUser($userId),
            'overdueCount' => $this->repository->countOverdueByUser($userId),
            'feedbackMessage' => $feedbackMessage,
            'error' => $error,
        ]);
    }

    public function create(): void
    {
        $this->render('tasks/create', [
            'pageTitle' => 'Нове завдання',
            'errors' => [],
            'old' => [],
        ]);
    }

    public function store(): void
    {
        $userId = (int) $_SESSION['user_id'];
        $title = trim($_POST['title'] ?? '');
        $deadlineRaw = trim($_POST['deadline'] ?? '');
        $deadline = $deadlineRaw !== '' ? $deadlineRaw : null;

        try {
            $this->repository->add($userId, $title, $deadline);
        } catch (ValidationException $e) {
            $this->render('tasks/create', [
                'pageTitle' => 'Нове завдання',
                'errors' => $e->getErrors(),
                'old' => ['title' => $title, 'deadline' => $deadlineRaw],
            ]);
            return;
        }

        $_SESSION['feedback'] = 'Завдання успішно додано!';
        $this->redirect('/');
    }

    public function complete(): void
    {
        try {
            $this->repository->complete((int) ($_POST['id'] ?? 0), (int) $_SESSION['user_id']);
            $_SESSION['feedback'] = 'Статус завдання оновлено!';
        } catch (TaskNotFoundException $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        $this->redirect('/');
    }

    public function delete(): void
    {
        try {
            $this->repository->delete((int) ($_POST['id'] ?? 0), (int) $_SESSION['user_id']);
            $_SESSION['feedback'] = 'Завдання видалено з бази даних!';
        } catch (TaskNotFoundException $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        $this->redirect('/');
    }
}

<?php
declare(strict_types=1);

class ValidationException extends TaskManagerException
{
    private array $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct('Помилка валідації даних форми: ' . implode(', ', $errors), 422);
    }

    public function getErrors(): array { return $this->errors; }
}

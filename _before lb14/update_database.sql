USE `task_manager`;

ALTER TABLE `tasks`
    ADD COLUMN `deadline` DATETIME NULL,
    ADD COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ADD COLUMN `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    ADD INDEX `idx_tasks_user_id` (`user_id`),
    ADD CONSTRAINT `fk_tasks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

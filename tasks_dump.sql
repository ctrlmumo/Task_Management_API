-- INSTRUCTIONS TO RUN THIS DUMP:
--   1. Open MySQL client or phpMyAdmin
--   2. Run:  source /path/to/tasks_dump.sql
--      OR:   mysql -u root -p < tasks_dump.sql
--
-- Alternatively, skip this dump and run:
--   php artisan migrate - creates the table from the migration file
--   php artisan db:seed - inserts the demo rows from TaskSeeder.php

CREATE DATABASE IF NOT EXISTS `task_management`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `task_management`;

CREATE TABLE IF NOT EXISTS `migrations` (
  `id`        int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch`     int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`) VALUES
  ('2026_03_30_000000_create_tasks_table', 1);

DROP TABLE IF EXISTS `tasks`;

CREATE TABLE `tasks` (
  `id`         bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`      varchar(255) NOT NULL COMMENT 'Task title',
  `due_date`   date NOT NULL COMMENT 'Task deadline — must be today or in the future on creation',
  `priority`   enum('low','medium','high') NOT NULL DEFAULT 'medium' COMMENT 'Task urgency level',
  `status`     enum('pending','in_progress','done') NOT NULL DEFAULT 'pending' COMMENT 'Workflow state — can only progress forward',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `tasks_title_due_date_unique` (`title`, `due_date`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Task management system — all task records';

INSERT INTO `tasks` (`title`, `due_date`, `priority`, `status`, `created_at`, `updated_at`) VALUES

-- high priority
('Fix critical authentication bug',    DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'high',   'in_progress', NOW(), NOW()),
('Deploy hotfix to production',        DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'high',   'pending',     NOW(), NOW()),
('Security audit review',              DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'high',   'pending',     NOW(), NOW()),
('Database performance optimisation',  DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'high',   'done',        NOW(), NOW()),

-- medium priority
('Update API documentation',           DATE_ADD(CURDATE(), INTERVAL 4 DAY), 'medium', 'pending',     NOW(), NOW()),
('Code review for PR #47',             DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'medium', 'in_progress', NOW(), NOW()),
('Write unit tests for TaskController',DATE_ADD(CURDATE(), INTERVAL 5 DAY), 'medium', 'pending',     NOW(), NOW()),
('Refactor user service module',       DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'medium', 'done',        NOW(), NOW()),

-- low priority
('Update README with deployment steps',DATE_ADD(CURDATE(), INTERVAL 7 DAY),  'low',   'pending',     NOW(), NOW()),
('Clean up old feature branches',      DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'low',   'pending',     NOW(), NOW()),
('Upgrade NPM dependencies',           DATE_ADD(CURDATE(), INTERVAL 6 DAY),  'low',   'in_progress', NOW(), NOW());




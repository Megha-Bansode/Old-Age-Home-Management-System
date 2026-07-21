-- MySQL database schema for OAHMS (Old Age Home Management System)

CREATE DATABASE IF NOT EXISTS `oahms` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `oahms`;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `phone` VARCHAR(20) DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('super_admin', 'admin', 'caretaker', 'doctor', 'donor', 'family_member') NOT NULL,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `address` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_username (`username`),
  INDEX idx_email (`email`),
  INDEX idx_role (`role`),
  INDEX idx_status (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Seeding default data for users
-- --------------------------------------------------------

-- Default password is 'admin123' hashed with bcrypt
INSERT INTO `users` (`first_name`, `last_name`, `username`, `email`, `phone`, `password`, `role`, `status`, `address`) VALUES
('Rohit', 'Yadav', 'rohit_admin', 'rohit@email.com', '9876543210', '$2y$10$Y148UoR7kFjUe7ZzYvYcO.fKx8s2r.i1lI.p8JgWf7x.7yGv9nKym', 'super_admin', 'active', 'Admin Block, Old Age Home, Sector 5, Delhi'),
('Anita', 'Sharma', 'anita_staff', 'anita@email.com', '9123456780', '$2y$10$Y148UoR7kFjUe7ZzYvYcO.fKx8s2r.i1lI.p8JgWf7x.7yGv9nKym', 'caretaker', 'inactive', 'Staff Quarter No. 12, Delhi'),
('Dr. Robert', 'Watson', 'robert_doctor', 'robert.doctor@email.com', '+1555987654', '$2y$10$Y148UoR7kFjUe7ZzYvYcO.fKx8s2r.i1lI.p8JgWf7x.7yGv9nKym', 'doctor', 'active', 'Sector 15, Dwarka, Delhi'),
('Alice', 'Green', 'alice_donor', 'alice.donor@email.com', '+1444555666', '$2y$10$Y148UoR7kFjUe7ZzYvYcO.fKx8s2r.i1lI.p8JgWf7x.7yGv9nKym', 'donor', 'active', 'Park Avenue, New York'),
('David', 'Vance', 'david_family', 'david.family@email.com', '+1333222111', '$2y$10$Y148UoR7kFjUe7ZzYvYcO.fKx8s2r.i1lI.p8JgWf7x.7yGv9nKym', 'family_member', 'active', 'Main Street, Seattle');

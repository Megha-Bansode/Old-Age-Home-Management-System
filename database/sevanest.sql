-- ============================================================
-- SevaNest - Old Age Home Management System Database Schema
-- Database: sevanest
-- Technology: MySQL / MariaDB
-- ============================================================

CREATE DATABASE IF NOT EXISTS `sevanest` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sevanest`;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `remember_tokens`;
DROP TABLE IF EXISTS `password_resets`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `phone` VARCHAR(20) DEFAULT NULL,
  `gender` VARCHAR(20) DEFAULT 'Male',
  `address` TEXT DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('Super Admin', 'Admin', 'Caretaker', 'Doctor', 'Donor', 'Family Member') NOT NULL,
  `status` ENUM('active', 'disabled') NOT NULL DEFAULT 'active',
  `profile_photo` VARCHAR(255) DEFAULT 'default_avatar.png',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email_role` (`email`, `role`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `password_resets`
-- --------------------------------------------------------

CREATE TABLE `password_resets` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `otp` VARCHAR(10) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `is_used` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_email_otp` (`email`, `otp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `remember_tokens`
-- --------------------------------------------------------

CREATE TABLE `remember_tokens` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `token_hash` VARCHAR(64) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_token` (`token_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Seed Sample Users for Demo
-- Super Admin: superadmin@sevanest.com / Super@123
-- Admin: admin@sevanest.com / Admin@123
-- Doctor: doctor@sevanest.com / Doctor@123
-- Caretaker: caretaker@sevanest.com / Care@123
-- Family Member: family@sevanest.com / Family@123
-- Donor: donor@sevanest.com / Donor@123
-- Disabled Account: disabled@sevanest.com / Disabled@123
-- --------------------------------------------------------

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `gender`, `address`, `password`, `role`, `status`, `profile_photo`) VALUES
(1, 'Rajesh Sharma', 'superadmin@sevanest.com', '9876543210', 'Male', '7th Floor, Admin Tower, Bangalore, KA', '$2y$10$Awqzgb.l2zp4NUKwvmo9k.PzKcPvhUwfcHqyxYXNuBkze5Rs4CRVi', 'Super Admin', 'active', 'superadmin.png'),
(2, 'Anita Verma', 'admin@sevanest.com', '9876543211', 'Female', '55 Temple Street, Chennai, TN', '$2y$10$Ym.BPavgUj/Sd3U7bzUUIuC3XwV2l5LEA8aEwHtbxhrjEoWEYRtfC', 'Admin', 'active', 'admin.png'),
(3, 'Dr. Priya Nair', 'doctor@sevanest.com', '9876543213', 'Female', 'Suite 4B, Care Avenue, Mumbai, MH', '$2y$10$ymD.tGsmd.JKPf2Ktq0AK./jAJdg1yzRtdVFAksa6u3dzJL10UpcK', 'Doctor', 'active', 'doctor.png'),
(4, 'Suresh Kumar', 'caretaker@sevanest.com', '9876543212', 'Male', 'House 24, Sunshine Colony, Pune, MH', '$2y$10$.AoPyF7DFHZlFQzrUZObpOeElOQ1SQzw0bAWSw419bUNESoVkpPlm', 'Caretaker', 'active', 'caretaker.png'),
(5, 'Sunita Deshmukh', 'family@sevanest.com', '9876543215', 'Female', 'Block C, Green Park, New Delhi', '$2y$10$rd3bpHQy7kNQ9C4ZVNLF8emDY9K61Sk3abMOy4TjZyAjx5MfwI15C', 'Family Member', 'active', 'family.png'),
(6, 'Vikramaditya Mehta', 'donor@sevanest.com', '9876543214', 'Male', 'Plot 12, Riverbed Road, Nagpur, MH', '$2y$10$LpSYCkliA6zQv07fQ4Yqr.bhCjfjPdNrLSOLD.ht0gQ.VQgUcRJUq', 'Donor', 'active', 'donor.png'),
(7, 'Ramesh Chandra (Disabled)', 'disabled@sevanest.com', '9876543216', 'Male', 'Plot 12, Riverbed Road, Nagpur, MH', '$2y$10$MzbSYS1i7LCOD8KvFIfJdOXJEq1MsC0qwUs.SsbP.8QbPLPx9sAhG', 'Caretaker', 'disabled', 'default_avatar.png');

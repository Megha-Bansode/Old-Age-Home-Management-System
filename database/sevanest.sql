-- ============================================================
-- SevaNest - Old Age Home Management System Database Schema
-- Database: sevanest
-- Technology: MySQL / MariaDB
-- ============================================================

CREATE DATABASE IF NOT EXISTS `sevanest` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sevanest`;

-- --------------------------------------------------------
-- Drop existing tables in reverse dependency order
-- --------------------------------------------------------

DROP TABLE IF EXISTS `emergency_cases`;
DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `inventory`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `pledges`;
DROP TABLE IF EXISTS `donations`;
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `visit_requests`;
DROP TABLE IF EXISTS `special_care`;
DROP TABLE IF EXISTS `medicine_log`;
DROP TABLE IF EXISTS `meal_plan`;
DROP TABLE IF EXISTS `meals`;
DROP TABLE IF EXISTS `activities`;
DROP TABLE IF EXISTS `attendance`;
DROP TABLE IF EXISTS `health_records`;
DROP TABLE IF EXISTS `medical_reports`;
DROP TABLE IF EXISTS `followups`;
DROP TABLE IF EXISTS `prescriptions`;
DROP TABLE IF EXISTS `patient_assignments`;
DROP TABLE IF EXISTS `appointments`;
DROP TABLE IF EXISTS `staff_leave`;
DROP TABLE IF EXISTS `staff_shifts`;
DROP TABLE IF EXISTS `staff`;
DROP TABLE IF EXISTS `discharges`;
DROP TABLE IF EXISTS `admissions`;
DROP TABLE IF EXISTS `residents`;
DROP TABLE IF EXISTS `rooms`;
DROP TABLE IF EXISTS `remember_tokens`;
DROP TABLE IF EXISTS `password_resets`;
DROP TABLE IF EXISTS `users`;

-- --------------------------------------------------------
-- 1. Users & Auth Security
-- --------------------------------------------------------

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
-- 2. Rooms & Residents
-- --------------------------------------------------------

CREATE TABLE `rooms` (
  `room_id` INT AUTO_INCREMENT PRIMARY KEY,
  `room_number` VARCHAR(20) NOT NULL UNIQUE,
  `room_type` ENUM('Single', 'Shared', 'Ward', 'ICU') NOT NULL,
  `capacity` INT NOT NULL,
  `occupancy` INT NOT NULL DEFAULT 0,
  `status` ENUM('Available', 'Full', 'Maintenance') NOT NULL DEFAULT 'Available',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_rooms_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `residents` (
  `resident_id` INT AUTO_INCREMENT PRIMARY KEY,
  `resident_code` VARCHAR(50) NOT NULL UNIQUE,
  `full_name` VARCHAR(100) NOT NULL,
  `gender` VARCHAR(20) DEFAULT NULL,
  `date_of_birth` DATE DEFAULT NULL,
  `age` INT DEFAULT NULL,
  `blood_group` VARCHAR(10) DEFAULT NULL,
  `emergency_contact_name` VARCHAR(100) DEFAULT NULL,
  `emergency_contact_phone` VARCHAR(20) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `room_number` VARCHAR(20) DEFAULT NULL,
  `health_status` TEXT DEFAULT NULL,
  `admission_date` DATE DEFAULT NULL,
  `profile_photo` VARCHAR(255) DEFAULT 'default_resident.png',
  `status` ENUM('Active', 'Inactive', 'Discharged') NOT NULL DEFAULT 'Active',
  `family_member_id` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`room_number`) REFERENCES `rooms`(`room_number`) ON DELETE SET NULL,
  FOREIGN KEY (`family_member_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_residents_status` (`status`),
  INDEX `idx_residents_full_name` (`full_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. Admissions & Discharges
-- --------------------------------------------------------

CREATE TABLE `admissions` (
  `admission_id` INT AUTO_INCREMENT PRIMARY KEY,
  `resident_id` INT NOT NULL,
  `admission_date` DATE NOT NULL,
  `admitted_by` INT NOT NULL,
  `emergency_contact_name` VARCHAR(100) DEFAULT NULL,
  `emergency_contact_phone` VARCHAR(20) DEFAULT NULL,
  `initial_health_summary` TEXT DEFAULT NULL,
  `status` ENUM('Approved', 'Pending', 'Rejected') NOT NULL DEFAULT 'Approved',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`resident_id`) REFERENCES `residents`(`resident_id`) ON DELETE CASCADE,
  FOREIGN KEY (`admitted_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
  INDEX `idx_admissions_date` (`admission_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `discharges` (
  `discharge_id` INT AUTO_INCREMENT PRIMARY KEY,
  `resident_id` INT NOT NULL,
  `discharge_date` DATE NOT NULL,
  `discharged_by` INT NOT NULL,
  `reason` ENUM('Request', 'Medical', 'Deceased', 'Other') NOT NULL,
  `summary` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`resident_id`) REFERENCES `residents`(`resident_id`) ON DELETE CASCADE,
  FOREIGN KEY (`discharged_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
  INDEX `idx_discharges_date` (`discharge_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. Staff Management
-- --------------------------------------------------------

CREATE TABLE `staff` (
  `staff_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `staff_code` VARCHAR(50) NOT NULL UNIQUE,
  `full_name` VARCHAR(100) NOT NULL,
  `gender` VARCHAR(20) DEFAULT NULL,
  `date_of_birth` DATE DEFAULT NULL,
  `age` INT DEFAULT NULL,
  `department` VARCHAR(100) DEFAULT NULL,
  `designation` VARCHAR(100) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL UNIQUE,
  `address` TEXT DEFAULT NULL,
  `joining_date` DATE DEFAULT NULL,
  `salary` DECIMAL(10,2) DEFAULT NULL,
  `employment_type` ENUM('Permanent', 'Contract', 'Part-Time') NOT NULL DEFAULT 'Permanent',
  `shift` VARCHAR(50) DEFAULT NULL,
  `profile_photo` VARCHAR(255) DEFAULT 'default_staff.png',
  `status` ENUM('Active', 'On Leave', 'Resigned') NOT NULL DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_staff_status` (`status`),
  INDEX `idx_staff_department` (`department`),
  INDEX `idx_staff_full_name` (`full_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `staff_shifts` (
  `shift_id` INT AUTO_INCREMENT PRIMARY KEY,
  `staff_id` INT NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `shift_date` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`staff_id`) REFERENCES `staff`(`staff_id`) ON DELETE CASCADE,
  INDEX `idx_shifts_date` (`shift_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `staff_leave` (
  `leave_id` INT AUTO_INCREMENT PRIMARY KEY,
  `staff_id` INT NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `leave_type` ENUM('Sick', 'Casual', 'Earned', 'Unpaid') NOT NULL,
  `reason` TEXT DEFAULT NULL,
  `status` ENUM('Pending', 'Approved', 'Rejected') NOT NULL DEFAULT 'Pending',
  `approved_by` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`staff_id`) REFERENCES `staff`(`staff_id`) ON DELETE CASCADE,
  FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_leave_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 5. Appointments & Patient Assignments
-- --------------------------------------------------------

CREATE TABLE `appointments` (
  `appointment_id` INT AUTO_INCREMENT PRIMARY KEY,
  `resident_id` INT NOT NULL,
  `doctor_id` INT NOT NULL,
  `appointment_date` DATETIME NOT NULL,
  `reason` VARCHAR(255) NOT NULL,
  `status` ENUM('Scheduled', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Scheduled',
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`resident_id`) REFERENCES `residents`(`resident_id`) ON DELETE CASCADE,
  FOREIGN KEY (`doctor_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
  INDEX `idx_appointments_date` (`appointment_date`),
  INDEX `idx_appointments_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `patient_assignments` (
  `assignment_id` INT AUTO_INCREMENT PRIMARY KEY,
  `resident_id` INT NOT NULL,
  `caretaker_id` INT NOT NULL,
  `assigned_date` DATE NOT NULL,
  `status` ENUM('Active', 'Completed') NOT NULL DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`resident_id`) REFERENCES `residents`(`resident_id`) ON DELETE CASCADE,
  FOREIGN KEY (`caretaker_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
  INDEX `idx_assignments_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 6. Medical & Health Records
-- --------------------------------------------------------

CREATE TABLE `prescriptions` (
  `prescription_id` INT AUTO_INCREMENT PRIMARY KEY,
  `appointment_id` INT DEFAULT NULL,
  `resident_id` INT NOT NULL,
  `doctor_id` INT NOT NULL,
  `prescription_date` DATE NOT NULL,
  `diagnosis` TEXT DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`appointment_id`) REFERENCES `appointments`(`appointment_id`) ON DELETE SET NULL,
  FOREIGN KEY (`resident_id`) REFERENCES `residents`(`resident_id`) ON DELETE CASCADE,
  FOREIGN KEY (`doctor_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
  INDEX `idx_prescriptions_date` (`prescription_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `followups` (
  `followup_id` INT AUTO_INCREMENT PRIMARY KEY,
  `prescription_id` INT NOT NULL,
  `followup_date` DATE NOT NULL,
  `status` ENUM('Pending', 'Completed', 'Missed') NOT NULL DEFAULT 'Pending',
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions`(`prescription_id`) ON DELETE CASCADE,
  INDEX `idx_followups_date` (`followup_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `medical_reports` (
  `report_id` INT AUTO_INCREMENT PRIMARY KEY,
  `resident_id` INT NOT NULL,
  `report_name` VARCHAR(150) NOT NULL,
  `report_type` VARCHAR(50) DEFAULT NULL,
  `report_date` DATE NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `uploaded_by` INT NOT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`resident_id`) REFERENCES `residents`(`resident_id`) ON DELETE CASCADE,
  FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
  INDEX `idx_reports_date` (`report_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `health_records` (
  `record_id` INT AUTO_INCREMENT PRIMARY KEY,
  `resident_id` INT NOT NULL,
  `recorded_by` INT NOT NULL,
  `record_date` DATETIME NOT NULL,
  `systolic_bp` INT DEFAULT NULL,
  `diastolic_bp` INT DEFAULT NULL,
  `pulse` INT DEFAULT NULL,
  `temperature` DECIMAL(4,1) DEFAULT NULL,
  `blood_sugar` INT DEFAULT NULL,
  `weight` DECIMAL(5,2) DEFAULT NULL,
  `symptoms` TEXT DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`resident_id`) REFERENCES `residents`(`resident_id`) ON DELETE CASCADE,
  FOREIGN KEY (`recorded_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
  INDEX `idx_health_records_date` (`record_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 7. Staff Attendance
-- --------------------------------------------------------

CREATE TABLE `attendance` (
  `attendance_id` INT AUTO_INCREMENT PRIMARY KEY,
  `staff_id` INT NOT NULL,
  `attendance_date` DATE NOT NULL,
  `check_in` TIME DEFAULT NULL,
  `check_out` TIME DEFAULT NULL,
  `status` ENUM('Present', 'Absent', 'Half Day', 'Leave') NOT NULL DEFAULT 'Present',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`staff_id`) REFERENCES `staff`(`staff_id`) ON DELETE CASCADE,
  INDEX `idx_attendance_date` (`attendance_date`),
  INDEX `idx_attendance_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 8. Activities & Meals Management
-- --------------------------------------------------------

CREATE TABLE `activities` (
  `activity_id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `activity_date` DATE NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `location` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_activities_date` (`activity_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `meals` (
  `meal_id` INT AUTO_INCREMENT PRIMARY KEY,
  `meal_name` VARCHAR(100) NOT NULL,
  `meal_type` ENUM('Veg', 'Non-Veg', 'Diabetic-Friendly', 'Liquid', 'Soft') NOT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `meal_plan` (
  `plan_id` INT AUTO_INCREMENT PRIMARY KEY,
  `resident_id` INT NOT NULL,
  `meal_id` INT NOT NULL,
  `day_of_week` ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
  `notes` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`resident_id`) REFERENCES `residents`(`resident_id`) ON DELETE CASCADE,
  FOREIGN KEY (`meal_id`) REFERENCES `meals`(`meal_id`) ON DELETE CASCADE,
  INDEX `idx_meal_plan_day` (`day_of_week`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 9. Medicine Log & Special Care
-- --------------------------------------------------------

CREATE TABLE `medicine_log` (
  `log_id` INT AUTO_INCREMENT PRIMARY KEY,
  `prescription_id` INT NOT NULL,
  `resident_id` INT NOT NULL,
  `medicine_name` VARCHAR(100) NOT NULL,
  `dosage` VARCHAR(50) NOT NULL,
  `scheduled_time` TIME NOT NULL,
  `administered_time` DATETIME DEFAULT NULL,
  `administered_by` INT DEFAULT NULL,
  `status` ENUM('Scheduled', 'Given', 'Missed', 'Refused') NOT NULL DEFAULT 'Scheduled',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions`(`prescription_id`) ON DELETE CASCADE,
  FOREIGN KEY (`resident_id`) REFERENCES `residents`(`resident_id`) ON DELETE CASCADE,
  FOREIGN KEY (`administered_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_med_log_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `special_care` (
  `care_id` INT AUTO_INCREMENT PRIMARY KEY,
  `resident_id` INT NOT NULL,
  `instruction` TEXT NOT NULL,
  `status` ENUM('Active', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Active',
  `assigned_to` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`resident_id`) REFERENCES `residents`(`resident_id`) ON DELETE CASCADE,
  FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_special_care_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 10. Visit Requests & Messages
-- --------------------------------------------------------

CREATE TABLE `visit_requests` (
  `request_id` INT AUTO_INCREMENT PRIMARY KEY,
  `family_member_id` INT NOT NULL,
  `resident_id` INT NOT NULL,
  `visit_date` DATETIME NOT NULL,
  `purpose` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('Pending', 'Approved', 'Rejected') NOT NULL DEFAULT 'Pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`family_member_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`resident_id`) REFERENCES `residents`(`resident_id`) ON DELETE CASCADE,
  INDEX `idx_visit_date` (`visit_date`),
  INDEX `idx_visit_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `messages` (
  `message_id` INT AUTO_INCREMENT PRIMARY KEY,
  `sender_id` INT NOT NULL,
  `receiver_id` INT NOT NULL,
  `subject` VARCHAR(150) DEFAULT NULL,
  `body` TEXT NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`receiver_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_messages_sender_receiver` (`sender_id`, `receiver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 11. Donations & Pledges
-- --------------------------------------------------------

CREATE TABLE `donations` (
  `donation_id` INT AUTO_INCREMENT PRIMARY KEY,
  `donor_id` INT DEFAULT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `payment_method` ENUM('Cash', 'Card', 'UPI', 'Bank Transfer') NOT NULL,
  `transaction_id` VARCHAR(100) DEFAULT NULL,
  `purpose` VARCHAR(255) DEFAULT NULL,
  `donation_date` DATETIME NOT NULL,
  `receipt_number` VARCHAR(50) NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`donor_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_donation_date` (`donation_date`),
  INDEX `idx_receipt` (`receipt_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pledges` (
  `pledge_id` INT AUTO_INCREMENT PRIMARY KEY,
  `donor_id` INT NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `target_date` DATE NOT NULL,
  `status` ENUM('Pending', 'Fulfilled', 'Cancelled') NOT NULL DEFAULT 'Pending',
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`donor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_pledges_date` (`target_date`),
  INDEX `idx_pledges_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 12. Events & Inventory
-- --------------------------------------------------------

CREATE TABLE `events` (
  `event_id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `event_date` DATETIME NOT NULL,
  `location` VARCHAR(100) DEFAULT NULL,
  `created_by` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
  INDEX `idx_events_date` (`event_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `inventory` (
  `item_id` INT AUTO_INCREMENT PRIMARY KEY,
  `item_name` VARCHAR(100) NOT NULL,
  `item_category` ENUM('Food', 'Medical', 'Hygiene', 'Office', 'Other') NOT NULL,
  `quantity` INT NOT NULL DEFAULT 0,
  `unit` VARCHAR(20) NOT NULL,
  `min_quantity` INT NOT NULL DEFAULT 10,
  `last_restocked` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_inventory_category` (`item_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 13. System Notifications & Activity Logs
-- --------------------------------------------------------

CREATE TABLE `notifications` (
  `notification_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_notifications_user` (`user_id`),
  INDEX `idx_notifications_unread` (`user_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `activity_logs` (
  `log_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `action` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_activity_user` (`user_id`),
  INDEX `idx_activity_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 14. Emergency Management
-- --------------------------------------------------------

CREATE TABLE `emergency_cases` (
  `case_id` INT AUTO_INCREMENT PRIMARY KEY,
  `resident_id` INT NOT NULL,
  `reported_by` INT NOT NULL,
  `incident_description` TEXT NOT NULL,
  `action_taken` TEXT DEFAULT NULL,
  `hospital_name` VARCHAR(150) DEFAULT NULL,
  `status` ENUM('Active', 'Resolved') NOT NULL DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`resident_id`) REFERENCES `residents`(`resident_id`) ON DELETE CASCADE,
  FOREIGN KEY (`reported_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
  INDEX `idx_emergency_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Seed Sample Users for Demo
-- --------------------------------------------------------

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `gender`, `address`, `password`, `role`, `status`, `profile_photo`) VALUES
(1, 'Rajesh Sharma', 'superadmin@sevanest.com', '9876543210', 'Male', '7th Floor, Admin Tower, Bangalore, KA', '$2y$10$Awqzgb.l2zp4NUKwvmo9k.PzKcPvhUwfcHqyxYXNuBkze5Rs4CRVi', 'Super Admin', 'active', 'superadmin.png'),
(2, 'Anita Verma', 'admin@sevanest.com', '9876543211', 'Female', '55 Temple Street, Chennai, TN', '$2y$10$Ym.BPavgUj/Sd3U7bzUUIuC3XwV2l5LEA8aEwHtbxhrjEoWEYRtfC', 'Admin', 'active', 'admin.png'),
(3, 'Dr. Priya Nair', 'doctor@sevanest.com', '9876543213', 'Female', 'Suite 4B, Care Avenue, Mumbai, MH', '$2y$10$ymD.tGsmd.JKPf2Ktq0AK./jAJdg1yzRtdVFAksa6u3dzJL10UpcK', 'Doctor', 'active', 'doctor.png'),
(4, 'Suresh Kumar', 'caretaker@sevanest.com', '9876543212', 'Male', 'House 24, Sunshine Colony, Pune, MH', '$2y$10$.AoPyF7DFHZlFQzrUZObpOeElOQ1SQzw0bAWSw419bUNESoVkpPlm', 'Caretaker', 'active', 'caretaker.png'),
(5, 'Sunita Deshmukh', 'family@sevanest.com', '9876543215', 'Female', 'Block C, Green Park, New Delhi', '$2y$10$rd3bpHQy7kNQ9C4ZVNLF8emDY9K61Sk3abMOy4TjZyAjx5MfwI15C', 'Family Member', 'active', 'family.png'),
(6, 'Vikramaditya Mehta', 'donor@sevanest.com', '9876543214', 'Male', 'Plot 12, Riverbed Road, Nagpur, MH', '$2y$10$LpSYCkliA6zQv07fQ4Yqr.bhCjfjPdNrLSOLD.ht0gQ.VQgUcRJUq', 'Donor', 'active', 'donor.png'),
(7, 'Ramesh Chandra (Disabled)', 'disabled@sevanest.com', '9876543216', 'Male', 'Plot 12, Riverbed Road, Nagpur, MH', '$2y$10$MzbSYS1i7LCOD8KvFIfJdOXJEq1MsC0qwUs.SsbP.8QbPLPx9sAhG', 'Caretaker', 'disabled', 'default_avatar.png');

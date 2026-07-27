USE `sevanest`;

-- Default demo users seeding
-- Super Admin: superadmin@sevanest.com / Super@123
-- Admin: admin@sevanest.com / Admin@123
-- Doctor: doctor@sevanest.com / Doctor@123
-- Caretaker: caretaker@sevanest.com / Care@123
-- Family Member: family@sevanest.com / Family@123
-- Donor: donor@sevanest.com / Donor@123
-- Disabled Account: disabled@sevanest.com / Disabled@123

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password`, `role`, `status`, `profile_photo`) VALUES
(1, 'Rajesh Sharma', 'superadmin@sevanest.com', '9876543210', '$2y$10$Awqzgb.l2zp4NUKwvmo9k.PzKcPvhUwfcHqyxYXNuBkze5Rs4CRVi', 'Super Admin', 'active', 'superadmin.png'),
(2, 'Anita Verma', 'admin@sevanest.com', '9876543211', '$2y$10$Ym.BPavgUj/Sd3U7bzUUIuC3XwV2l5LEA8aEwHtbxhrjEoWEYRtfC', 'Admin', 'active', 'admin.png'),
(3, 'Dr. Priya Nair', 'doctor@sevanest.com', '9876543213', '$2y$10$ymD.tGsmd.JKPf2Ktq0AK./jAJdg1yzRtdVFAksa6u3dzJL10UpcK', 'Doctor', 'active', 'doctor.png'),
(4, 'Suresh Kumar', 'caretaker@sevanest.com', '9876543212', '$2y$10$.AoPyF7DFHZlFQzrUZObpOeElOQ1SQzw0bAWSw419bUNESoVkpPlm', 'Caretaker', 'active', 'caretaker.png'),
(5, 'Sunita Deshmukh', 'family@sevanest.com', '9876543215', '$2y$10$rd3bpHQy7kNQ9C4ZVNLF8emDY9K61Sk3abMOy4TjZyAjx5MfwI15C', 'Family Member', 'active', 'family.png'),
(6, 'Vikramaditya Mehta', 'donor@sevanest.com', '9876543214', '$2y$10$LpSYCkliA6zQv07fQ4Yqr.bhCjfjPdNrLSOLD.ht0gQ.VQgUcRJUq', 'Donor', 'active', 'donor.png'),
(7, 'Ramesh Chandra (Disabled)', 'disabled@sevanest.com', '9876543216', '$2y$10$MzbSYS1i7LCOD8KvFIfJdOXJEq1MsC0qwUs.SsbP.8QbPLPx9sAhG', 'Caretaker', 'disabled', 'default_avatar.png')
ON DUPLICATE KEY UPDATE 
    `full_name` = VALUES(`full_name`),
    `phone` = VALUES(`phone`),
    `password` = VALUES(`password`),
    `role` = VALUES(`role`),
    `status` = VALUES(`status`),
    `profile_photo` = VALUES(`profile_photo`);

-- Seeding homes table
INSERT INTO `homes` (`id`, `name`, `type`, `city`, `area`, `pincode`, `address`, `latitude`, `longitude`, `has_medical_facility`, `wheelchair_accessible`, `phone`) VALUES
(1, 'Sunrise Residency', 'private', 'Pune', 'Baner', '411045', '12 Baner Road, Pune', 18.5590, 73.7868, 1, 1, '020-12345678'),
(2, 'Shanti Niwas Home', 'government', 'Pune', 'Kothrud', '411038', '4 Kothrud Main Rd, Pune', 18.5074, 73.8077, 1, 0, '020-23456789'),
(3, 'Golden Years Care', 'ngo', 'Pune', 'Hadapsar', '411028', '9 Hadapsar Circle, Pune', 18.5089, 73.9260, 0, 1, '020-34567890'),
(4, 'Amrit Ashram', 'private', 'Pune', 'Viman Nagar', '411014', '21 Viman Nagar, Pune', 18.5679, 73.9143, 1, 1, '020-45678901')
ON DUPLICATE KEY UPDATE 
    `name` = VALUES(`name`),
    `type` = VALUES(`type`),
    `city` = VALUES(`city`),
    `area` = VALUES(`area`),
    `pincode` = VALUES(`pincode`),
    `address` = VALUES(`address`),
    `latitude` = VALUES(`latitude`),
    `longitude` = VALUES(`longitude`),
    `has_medical_facility` = VALUES(`has_medical_facility`),
    `wheelchair_accessible` = VALUES(`wheelchair_accessible`),
    `phone` = VALUES(`phone`);

-- Seeding doctor_visits table
INSERT INTO `doctor_visits` (`id`, `home_id`, `title`, `doctor_name`, `visit_date`, `status`, `notes`) VALUES
(1, 1, 'General Physician Visit', 'Dr. Kavita Sharma', '2026-07-25', 'confirmed', 'Routine checkup for all residents'),
(2, 2, 'Eye Specialist Camp', 'Dr. Anil Deshmukh', '2026-07-28', 'confirmed', 'Free vision screening'),
(3, 3, 'Dental Camp', 'Dr. Priya Nair', '2026-07-30', 'planned', 'Dental checkup and cleaning'),
(4, 4, 'Health Awareness Camp', 'Dr. Rohan Kulkarni', '2026-08-03', 'planned', 'General wellness talk')
ON DUPLICATE KEY UPDATE 
    `home_id` = VALUES(`home_id`),
    `title` = VALUES(`title`),
    `doctor_name` = VALUES(`doctor_name`),
    `visit_date` = VALUES(`visit_date`),
    `status` = VALUES(`status`),
    `notes` = VALUES(`notes`);

-- Seeding testimonials table
INSERT INTO `testimonials` (`id`, `author_name`, `role`, `message`, `rating`) VALUES
(1, 'Anita Rao', 'Administrator', 'Managing residents has never been easier - everything we need is in one place.', 5),
(2, 'Rakesh Mehta', 'Donor', 'The donation tracking system is completely transparent, so I always know where my giving goes.', 5),
(3, 'Sunita Iyer', 'Family Member', 'I found a trusted old age home for my father within minutes of searching.', 5)
ON DUPLICATE KEY UPDATE 
    `author_name` = VALUES(`author_name`),
    `role` = VALUES(`role`),
    `message` = VALUES(`message`),
    `rating` = VALUES(`rating`);

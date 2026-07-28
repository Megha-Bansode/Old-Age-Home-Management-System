USE `sevanest`;

-- Default demo users seeding
-- Super Admin: superadmin@sevanest.com / Super@123
-- Admin: admin@sevanest.com / Admin@123
-- Doctor: doctor@sevanest.com / Doctor@123
-- Caretaker: caretaker@sevanest.com / Care@123
-- Family Member: family@sevanest.com / Family@123
-- Donor: donor@sevanest.com / Donor@123
-- Disabled Account: disabled@sevanest.com / Disabled@123

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `gender`, `address`, `password`, `role`, `status`, `profile_photo`) VALUES
(1, 'Rajesh Sharma', 'superadmin@sevanest.com', '9876543210', 'Male', '7th Floor, Admin Tower, Bangalore, KA', '$2y$10$Awqzgb.l2zp4NUKwvmo9k.PzKcPvhUwfcHqyxYXNuBkze5Rs4CRVi', 'Super Admin', 'active', 'superadmin.png'),
(2, 'Anita Verma', 'admin@sevanest.com', '9876543211', 'Female', '55 Temple Street, Chennai, TN', '$2y$10$Ym.BPavgUj/Sd3U7bzUUIuC3XwV2l5LEA8aEwHtbxhrjEoWEYRtfC', 'Admin', 'active', 'admin.png'),
(3, 'Dr. Priya Nair', 'doctor@sevanest.com', '9876543213', 'Female', 'Suite 4B, Care Avenue, Mumbai, MH', '$2y$10$ymD.tGsmd.JKPf2Ktq0AK./jAJdg1yzRtdVFAksa6u3dzJL10UpcK', 'Doctor', 'active', 'doctor.png'),
(4, 'Suresh Kumar', 'caretaker@sevanest.com', '9876543212', 'Male', 'House 24, Sunshine Colony, Pune, MH', '$2y$10$.AoPyF7DFHZlFQzrUZObpOeElOQ1SQzw0bAWSw419bUNESoVkpPlm', 'Caretaker', 'active', 'caretaker.png'),
(5, 'Sunita Deshmukh', 'family@sevanest.com', '9876543215', 'Female', 'Block C, Green Park, New Delhi', '$2y$10$rd3bpHQy7kNQ9C4ZVNLF8emDY9K61Sk3abMOy4TjZyAjx5MfwI15C', 'Family Member', 'active', 'family.png'),
(6, 'Vikramaditya Mehta', 'donor@sevanest.com', '9876543214', 'Male', 'Plot 12, Riverbed Road, Nagpur, MH', '$2y$10$LpSYCkliA6zQv07fQ4Yqr.bhCjfjPdNrLSOLD.ht0gQ.VQgUcRJUq', 'Donor', 'active', 'donor.png'),
(7, 'Ramesh Chandra (Disabled)', 'disabled@sevanest.com', '9876543216', 'Male', 'Plot 12, Riverbed Road, Nagpur, MH', '$2y$10$MzbSYS1i7LCOD8KvFIfJdOXJEq1MsC0qwUs.SsbP.8QbPLPx9sAhG', 'Caretaker', 'disabled', 'default_avatar.png')
ON DUPLICATE KEY UPDATE 
    `full_name` = VALUES(`full_name`),
    `phone` = VALUES(`phone`),
    `gender` = VALUES(`gender`),
    `address` = VALUES(`address`),
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

-- Seeding residents table
INSERT INTO `residents` (
  `resident_id`, `resident_code`, `full_name`, `gender`, `date_of_birth`, `age`, `blood_group`, 
  `emergency_contact_name`, `emergency_contact_phone`, `address`, `room_number`, `health_status`, 
  `admission_date`, `profile_photo`, `status`
) VALUES
(1, 'RES001', 'Devendra Joshi', 'Male', '1945-05-15', 81, 'O+', 'Rohan Joshi', '9876500001', 'Flat 4A, Green Meadows, Mumbai', 'Room 101', 'Stable health, under regular observation for pressure.', '2024-01-10', 'resident1.png', 'Active'),
(2, 'RES002', 'Savitri Devi', 'Female', '1950-08-20', 75, 'A+', 'Priya Sharma', '9876500002', 'House 55, Temple Street, Chennai', 'Room 102', 'Mild hypertension, requires low-sodium meals.', '2024-02-15', 'resident2.png', 'Active'),
(3, 'RES003', 'Hari Haran', 'Male', '1942-12-10', 83, 'B+', 'Suresh Haran', '9876500003', 'Sector 15, Vashi, Navi Mumbai', 'Room 103', 'Diabetic, daily insulin injections at 9 AM.', '2024-03-01', 'resident3.png', 'Active'),
(4, 'RES004', 'Nirmala Deshpande', 'Female', '1948-03-25', 78, 'AB+', 'Amit Deshpande', '9876500004', 'Block C, Green Park, New Delhi', 'Room 104', 'Severe arthritis, uses a wheelchair for mobility.', '2024-04-12', 'resident4.png', 'Inactive'),
(5, 'RES005', 'Balraj Sahni', 'Male', '1940-11-05', 85, 'O-', 'Vikram Sahni', '9876500005', 'Plot 12, Riverbed Road, Nagpur', 'Room 105', 'Fully recovered from minor fracture. Discharged to family.', '2023-06-20', 'resident5.png', 'Discharged')
ON DUPLICATE KEY UPDATE 
    `resident_code` = VALUES(`resident_code`),
    `full_name` = VALUES(`full_name`),
    `gender` = VALUES(`gender`),
    `date_of_birth` = VALUES(`date_of_birth`),
    `age` = VALUES(`age`),
    `blood_group` = VALUES(`blood_group`),
    `emergency_contact_name` = VALUES(`emergency_contact_name`),
    `emergency_contact_phone` = VALUES(`emergency_contact_phone`),
    `address` = VALUES(`address`),
    `room_number` = VALUES(`room_number`),
    `health_status` = VALUES(`health_status`),
    `admission_date` = VALUES(`admission_date`),
    `profile_photo` = VALUES(`profile_photo`),
    `status` = VALUES(`status`);

-- Seeding staff table
INSERT INTO `staff` (
  `staff_id`, `staff_code`, `full_name`, `gender`, `date_of_birth`, `age`, `department`, 
  `designation`, `phone`, `email`, `address`, `joining_date`, `salary`, `employment_type`, 
  `shift`, `profile_photo`, `status`
) VALUES
(1, 'STF001', 'Ramesh Kadam', 'Male', '1985-04-12', 41, 'Nursing', 'Head Nurse', '9876540001', 'ramesh@sevanest.com', 'Baner, Pune, MH', '2022-03-15', 45000.00, 'Permanent', 'Day Shift', 'staff1.png', 'Active'),
(2, 'STF002', 'Sunita Patil', 'Female', '1990-07-22', 36, 'Kitchen', 'Head Cook', '9876540002', 'sunita@sevanest.com', 'Kothrud, Pune, MH', '2023-01-10', 25000.00, 'Permanent', 'Morning Shift', 'staff2.png', 'Active'),
(3, 'STF003', 'Amit Joshi', 'Male', '1993-11-05', 32, 'Administration', 'Front Desk Officer', '9876540003', 'amit@sevanest.com', 'Hadapsar, Pune, MH', '2024-05-01', 30000.00, 'Permanent', 'General Shift', 'staff3.png', 'Active'),
(4, 'STF004', 'Rekha Sawant', 'Female', '1988-09-18', 37, 'Housekeeping', 'Cleaning Staff', '9876540004', 'rekha@sevanest.com', 'Viman Nagar, Pune, MH', '2023-06-20', 18000.00, 'Contract', 'Evening Shift', 'staff4.png', 'On Leave'),
(5, 'STF005', 'Vijay Shinde', 'Male', '1980-01-30', 46, 'Security', 'Senior Guard', '9876540005', 'vijay@sevanest.com', 'Baner, Pune, MH', '2021-11-01', 20000.00, 'Part-Time', 'Night Shift', 'staff5.png', 'Resigned')
ON DUPLICATE KEY UPDATE 
    `staff_code` = VALUES(`staff_code`),
    `full_name` = VALUES(`full_name`),
    `gender` = VALUES(`gender`),
    `date_of_birth` = VALUES(`date_of_birth`),
    `age` = VALUES(`age`),
    `department` = VALUES(`department`),
    `designation` = VALUES(`designation`),
    `phone` = VALUES(`phone`),
    `email` = VALUES(`email`),
    `address` = VALUES(`address`),
    `joining_date` = VALUES(`joining_date`),
    `salary` = VALUES(`salary`),
    `employment_type` = VALUES(`employment_type`),
    `shift` = VALUES(`shift`),
    `profile_photo` = VALUES(`profile_photo`),
    `status` = VALUES(`status`);

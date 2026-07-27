USE sevanest;

-- Default admin login: admin@sevanest.org / Admin@123
-- Change this password immediately after first login in production.
INSERT INTO users (full_name, email, phone, password, role) VALUES
('SevaNest Admin', 'admin@sevanest.com', '9000000000', '$2y$10$Ym.BPavgUj/Sd3U7bzUUIuC3XwV2l5LEA8aEwHtbxhrjEoWEYRtfC', 'Admin');

INSERT INTO homes (name, type, city, area, pincode, address, latitude, longitude, has_medical_facility, wheelchair_accessible, phone) VALUES
('Sunrise Residency', 'private', 'Pune', 'Baner', '411045', '12 Baner Road, Pune', 18.5590, 73.7868, 1, 1, '020-12345678'),
('Shanti Niwas Home', 'government', 'Pune', 'Kothrud', '411038', '4 Kothrud Main Rd, Pune', 18.5074, 73.8077, 1, 0, '020-23456789'),
('Golden Years Care', 'ngo', 'Pune', 'Hadapsar', '411028', '9 Hadapsar Circle, Pune', 18.5089, 73.9260, 0, 1, '020-34567890'),
('Amrit Ashram', 'private', 'Pune', 'Viman Nagar', '411014', '21 Viman Nagar, Pune', 18.5679, 73.9143, 1, 1, '020-45678901');

INSERT INTO doctor_visits (home_id, title, doctor_name, visit_date, status, notes) VALUES
(1, 'General Physician Visit', 'Dr. Kavita Sharma', '2026-07-25', 'confirmed', 'Routine checkup for all residents'),
(2, 'Eye Specialist Camp', 'Dr. Anil Deshmukh', '2026-07-28', 'confirmed', 'Free vision screening'),
(3, 'Dental Camp', 'Dr. Priya Nair', '2026-07-30', 'planned', 'Dental checkup and cleaning'),
(4, 'Health Awareness Camp', 'Dr. Rohan Kulkarni', '2026-08-03', 'planned', 'General wellness talk');

INSERT INTO testimonials (author_name, role, message, rating) VALUES
('Anita Rao', 'Administrator', 'Managing residents has never been easier - everything we need is in one place.', 5),
('Rakesh Mehta', 'Donor', 'The donation tracking system is completely transparent, so I always know where my giving goes.', 5),
('Sunita Iyer', 'Family Member', 'I found a trusted old age home for my father within minutes of searching.', 5);

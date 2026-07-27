<?php
/**
 * SevaNest — Admin Module Mock Data Helper Functions
 */

if (!function_exists('sn_e')) {
    function sn_e($v) {
        return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('sn_dashboard_stats')) {
    function sn_dashboard_stats() {
        return [
            [
                'icon'    => 'users',
                'delta'   => '+4 this week',
                'down'    => false,
                'value'   => '86',
                'label'   => 'Total residents',
                'variant' => ''
            ],
            [
                'icon'    => 'badge',
                'delta'   => '2 pending',
                'down'    => false,
                'value'   => '24',
                'label'   => 'Active staff',
                'variant' => ''
            ],
            [
                'icon'    => 'clipboard',
                'delta'   => '+1 today',
                'down'    => false,
                'value'   => '3',
                'label'   => 'Pending admissions',
                'variant' => 'warning'
            ],
            [
                'icon'    => 'visitor',
                'delta'   => '12 checked in',
                'down'    => false,
                'value'   => '12',
                'label'   => 'Visitors today',
                'variant' => ''
            ]
        ];
    }
}

if (!function_exists('sn_recent_admissions')) {
    function sn_recent_admissions() {
        return [
            ['name' => 'Kamala Devi', 'age' => 74, 'room' => 'A-102', 'date' => '2026-07-26', 'guardian' => 'Ravi Devi', 'status' => 'Admitted', 'id' => 'REQ-1001', 'phone' => '98450 11234'],
            ['name' => 'Harish Mehta', 'age' => 81, 'room' => 'B-204', 'date' => '2026-07-25', 'guardian' => 'Sunita Mehta', 'status' => 'Under Review', 'id' => 'REQ-1002', 'phone' => '98450 11235'],
            ['name' => 'Gopal Prasad', 'age' => 79, 'room' => 'C-105', 'date' => '2026-07-24', 'guardian' => 'Anil Prasad', 'status' => 'Pending', 'id' => 'REQ-1003', 'phone' => '98450 11236'],
        ];
    }
}

if (!function_exists('sn_admissions')) {
    function sn_admissions() {
        return [
            ['id' => 'REQ-1001', 'name' => 'Kamala Devi', 'age' => 74, 'requested' => '2026-07-26', 'guardian' => 'Ravi Devi', 'phone' => '98450 11234', 'status' => 'Approved'],
            ['id' => 'REQ-1002', 'name' => 'Harish Mehta', 'age' => 81, 'requested' => '2026-07-25', 'guardian' => 'Sunita Mehta', 'phone' => '98450 11235', 'status' => 'Under Review'],
            ['id' => 'REQ-1003', 'name' => 'Gopal Prasad', 'age' => 79, 'requested' => '2026-07-24', 'guardian' => 'Anil Prasad', 'phone' => '98450 11236', 'status' => 'Pending'],
            ['id' => 'REQ-1004', 'name' => 'Savitri Bai', 'age' => 83, 'requested' => '2026-07-22', 'guardian' => 'Kirti Bai', 'phone' => '98450 11237', 'status' => 'Rejected'],
        ];
    }
}

if (!function_exists('sn_discharges')) {
    function sn_discharges() {
        return [
            ['name' => 'Kamala Devi', 'room' => 'A-102', 'date' => '2026-08-01', 'reason' => 'Family taking care', 'handedTo' => 'Ravi Devi (Son)', 'status' => 'Scheduled'],
            ['name' => 'Ram Sharan', 'room' => 'B-105', 'date' => '2026-07-20', 'reason' => 'Relocating to another city', 'handedTo' => 'Priya Sharan (Daughter)', 'status' => 'Completed'],
        ];
    }
}

if (!function_exists('sn_residents')) {
    function sn_residents() {
        return [
            ['id' => 'RES-2001', 'name' => 'Kamala Devi', 'age' => 74, 'room' => 'A-102', 'admission' => '2025-04-12', 'guardian' => 'Ravi Devi', 'phone' => '98450 11234', 'health' => 'Stable', 'status' => 'Active'],
            ['id' => 'RES-2002', 'name' => 'Harish Mehta', 'age' => 81, 'room' => 'B-204', 'admission' => '2025-06-15', 'guardian' => 'Sunita Mehta', 'phone' => '98450 11235', 'health' => 'Stable', 'status' => 'Active'],
            ['id' => 'RES-2003', 'name' => 'Devaki Amma', 'age' => 85, 'room' => 'C-103', 'admission' => '2024-11-20', 'guardian' => 'Suresh Kumar', 'phone' => '98450 11238', 'health' => 'Needs Care', 'status' => 'Active'],
            ['id' => 'RES-2004', 'name' => 'Ram Sharan', 'age' => 78, 'room' => 'B-105', 'admission' => '2023-01-10', 'guardian' => 'Priya Sharan', 'phone' => '98450 11239', 'health' => 'Stable', 'status' => 'Discharged'],
        ];
    }
}

if (!function_exists('sn_staff')) {
    function sn_staff() {
        return [
            ['id' => 'STF-5001', 'name' => 'Meena Patil', 'role' => 'Caregiver', 'dept' => 'Care', 'shift' => 'Morning', 'phone' => '98765 43201', 'status' => 'On Duty'],
            ['id' => 'STF-5002', 'name' => 'Dr. Priya Nair', 'role' => 'Doctor', 'dept' => 'Medical', 'shift' => 'Morning', 'phone' => '98765 43202', 'status' => 'On Duty'],
            ['id' => 'STF-5003', 'name' => 'Suresh Kumar', 'role' => 'Caregiver', 'dept' => 'Care', 'shift' => 'Evening', 'phone' => '98765 43203', 'status' => 'Off Duty'],
            ['id' => 'STF-5004', 'name' => 'Ramesh Chandra', 'role' => 'Security', 'dept' => 'Operations', 'shift' => 'Night', 'phone' => '98765 43204', 'status' => 'On Leave'],
        ];
    }
}

if (!function_exists('sn_visitors')) {
    function sn_visitors() {
        return [
            ['name' => 'Ravi Devi', 'visiting' => 'Kamala Devi', 'relation' => 'Son', 'date' => '2026-07-27', 'checkin' => '10:15 AM', 'checkout' => '11:45 AM', 'status' => 'Checked Out'],
            ['name' => 'Sunita Mehta', 'visiting' => 'Harish Mehta', 'relation' => 'Daughter', 'date' => '2026-07-27', 'checkin' => '02:00 PM', 'checkout' => '—', 'status' => 'Checked In'],
            ['name' => 'Anil Prasad', 'visiting' => 'Gopal Prasad', 'relation' => 'Son', 'date' => '2026-07-28', 'checkin' => '09:00 AM', 'checkout' => '—', 'status' => 'Scheduled'],
        ];
    }
}

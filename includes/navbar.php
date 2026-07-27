<?php
/**
 * SevaNest — Shared Sub-Navbar Component
 * 
 * Expected Variables:
 *   - $page_title : Title of the page (string)
 *   - $module_name : Module name (default: "Super Admin Module")
 */

$sub_page_title = isset($page_title) ? $page_title : 'Dashboard';
$sub_module_name = isset($module_name) ? $module_name : 'Super Admin Module';
?>

<!-- ╔══════════════════════════════════════════════════════════╗
     ║  SEVANEST SUB-NAVBAR BREADCRUMB BAR                     ║
     ╚══════════════════════════════════════════════════════════╝ -->
<div class="sub-navbar-bar px-4 py-2 bg-white border-bottom shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-success-subtle text-success fw-bold px-2 py-1 rounded">
            <i class="bi bi-shield-check me-1"></i><?php echo htmlspecialchars($sub_module_name); ?>
        </span>
        <span class="text-muted opacity-50">/</span>
        <span class="fw-semibold text-dark fs-6"><?php echo htmlspecialchars($sub_page_title); ?></span>
    </div>
    
    <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center gap-2 text-muted small">
            <i class="bi bi-clock-history"></i>
            <span>System Status: <strong class="text-success">Online</strong></span>
        </div>
        <div class="vr opacity-25 d-none d-sm-block"></div>
        <div class="small text-muted d-none d-sm-block">
            <i class="bi bi-calendar3 me-1"></i><?php echo date('F j, Y'); ?>
        </div>
    </div>
</div>

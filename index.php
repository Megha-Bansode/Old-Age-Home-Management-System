<?php
/**
 * Entry point for the Old Age Home Admin Module.
 * Redirects into the dashboard; swap in an auth check here once
 * the login system is wired up, e.g.:
 *
 *   require_once __DIR__ . '/includes/config.php';
 *   if (empty($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
 */
header('Location: dashboard.php');
exit;

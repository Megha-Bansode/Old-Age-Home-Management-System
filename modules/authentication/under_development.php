<?php
/**
 * SevaNest — Temporary Module Under Development Page
 * Shown when a dashboard is not yet implemented.
 */

$role = isset($_GET['role']) ? htmlspecialchars($_GET['role']) : 'Requested';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Module Under Development — SevaNest</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- SevaNest Design System -->
    <link href="../../assets/css/variables.css" rel="stylesheet">
    <link href="../../assets/css/base.css" rel="stylesheet">
    <link href="../../assets/css/layout.css" rel="stylesheet">
    <link href="../../assets/css/components.css" rel="stylesheet">
    <link href="../../assets/css/animations.css" rel="stylesheet">
    <link href="../../assets/css/responsive.css" rel="stylesheet">

    <style>
        .dev-body {
            background: linear-gradient(135deg, var(--color-secondary) 0%, var(--color-bg-main) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .dev-card {
            background-color: var(--color-bg-card);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-large);
            box-shadow: var(--shadow-heavy);
            padding: var(--spacing-10) var(--spacing-8);
            text-align: center;
            max-width: 480px;
            width: 100%;
        }
        .dev-icon {
            font-size: 4rem;
            color: var(--color-accent);
            margin-bottom: var(--spacing-4);
            animation: oahms-pulse 2s infinite;
        }
        .dev-title {
            font-size: var(--font-size-xl);
            font-weight: 700;
            color: var(--color-text);
            margin-bottom: var(--spacing-2);
        }
        .dev-subtitle {
            font-size: var(--font-size-sm);
            color: var(--color-text-muted-team);
            line-height: 1.6;
            margin-bottom: var(--spacing-6);
        }
    </style>
</head>
<body class="dev-body">

<main class="container d-flex justify-content-center p-3">
    <div class="dev-card animate-scale-in">
        <div class="dev-icon" aria-hidden="true">
            <i class="bi bi-cone-striped"></i>
        </div>
        <h1 class="dev-title"><?php echo $role; ?> Dashboard</h1>
        <p class="dev-subtitle">
            This module is currently under active development. Our developers are working hard to bring this feature to life. Please check back later.
        </p>
        <a href="login.php" class="btn btn-primary w-100">
            <i class="bi bi-arrow-left me-2"></i> Return to Sign In
        </a>
    </div>
</main>

</body>
</html>

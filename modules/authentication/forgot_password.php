<?php
/**
 * SevaNest — Forgot Password Page
 * Authentication Module
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — SevaNest</title>
    <meta name="description" content="Reset your SevaNest account password safely.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 (CSS Reset/Grid basis) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- SevaNest Design System CSS -->
    <link href="../../assets/css/variables.css" rel="stylesheet">
    <link href="../../assets/css/base.css" rel="stylesheet">
    <link href="../../assets/css/layout.css" rel="stylesheet">
    <link href="../../assets/css/components.css" rel="stylesheet">
    <link href="../../assets/css/animations.css" rel="stylesheet">
    <link href="../../assets/css/responsive.css" rel="stylesheet">
    
    <!-- Forgot Password Page Custom CSS -->
    <link href="../../assets/css/forgot_password.css" rel="stylesheet">
</head>
<body class="auth-layout-body">

<main class="d-flex align-items-center justify-content-center min-vh-100 p-3">
    <div class="auth-card-container animate-scale-in">
        
        <!-- Logo Section -->
        <div class="auth-logo-section">
            <?php
            $logo_path = '../../assets/images/logo/logo.jpeg';
            if (file_exists($logo_path)):
            ?>
                <img src="<?php echo htmlspecialchars($logo_path); ?>" alt="SevaNest Logo" class="auth-brand-logo">
            <?php else: ?>
                <span class="auth-logo-text">SevaNest</span>
            <?php endif; ?>
        </div>

        <!-- Card Header Info -->
        <div class="auth-card-header">
            <h1 class="auth-card-title">Forgot Password</h1>
            <p class="auth-card-subtitle">Enter your registered email address and we'll send you an OTP to reset your password.</p>
        </div>

        <!-- Alert Container for displaying errors/success -->
        <div id="alert-container" aria-live="polite"></div>

        <!-- Forgot Password Form -->
        <form id="forgot-password-form" novalidate>
            <div class="form-group">
                <label for="email" class="form-label required">Email Address</label>
                <div class="input-icon-wrapper">
                    <span class="input-icon"><i class="bi bi-envelope"></i></span>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control" 
                           placeholder="name@example.com" 
                           required 
                           autocomplete="email">
                </div>
            </div>

            <button type="submit" id="send-otp-btn" class="btn btn-primary w-100 mt-md btn-hover-grow">
                <span class="btn-text">Send OTP</span>
            </button>
        </form>

        <!-- Card Footer Navigation -->
        <div class="auth-card-footer">
            <a href="login.php" class="back-link">
                <i class="bi bi-arrow-left"></i> Back to Login
            </a>
        </div>

    </div>
</main>

<!-- Shared validation scripts -->
<script src="../../assets/js/validation.js"></script>
<!-- Forgot password logic -->
<script src="../../assets/js/forgot_password.js"></script>

</body>
</html>

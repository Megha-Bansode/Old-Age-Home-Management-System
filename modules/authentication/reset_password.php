<?php
/**
 * SevaNest — Reset Password Page
 * Authentication Module
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — SevaNest</title>
    <meta name="description" content="Choose a strong new password to secure your account.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
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
    
    <!-- Reset Password Page Custom CSS -->
    <link href="../../assets/css/reset_password.css" rel="stylesheet">
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
            <h1 class="auth-card-title">Reset Password</h1>
            <p class="auth-card-subtitle">Choose a strong new password to protect and secure your account.</p>
        </div>

        <!-- Alert Container -->
        <div id="alert-container" aria-live="polite"></div>

        <!-- Reset Password Form -->
        <form id="reset-password-form" novalidate>
            <!-- New Password Input -->
            <div class="form-group">
                <label for="new-password" class="form-label required">New Password</label>
                <div class="password-input-wrapper">
                    <span class="input-icon"><i class="bi bi-lock"></i></span>
                    <input type="password" 
                           id="new-password" 
                           name="new-password" 
                           class="form-control" 
                           placeholder="Create new password" 
                           required 
                           autocomplete="new-password">
                    <button type="button" class="password-toggle-btn" aria-label="Show password" tabIndex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                
                <!-- Password Strength Indicator -->
                <div class="password-strength-wrapper">
                    <div class="strength-bar-container">
                        <div id="strength-bar" class="strength-bar"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-xs">
                        <span class="strength-label">Password Strength:</span>
                        <span id="strength-text" class="strength-text">Empty</span>
                    </div>
                </div>
            </div>

            <!-- Confirm Password Input -->
            <div class="form-group">
                <label for="confirm-password" class="form-label required">Confirm Password</label>
                <div class="password-input-wrapper">
                    <span class="input-icon"><i class="bi bi-lock-check"></i></span>
                    <input type="password" 
                           id="confirm-password" 
                           name="confirm-password" 
                           class="form-control" 
                           placeholder="Confirm new password" 
                           required 
                           autocomplete="new-password">
                    <button type="button" class="password-toggle-btn" aria-label="Show password" tabIndex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" id="reset-password-btn" class="btn btn-primary w-100 mt-md btn-hover-grow">
                <span class="btn-text">Reset Password</span>
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
<!-- Reset Password logic -->
<script src="../../assets/js/reset_password.js"></script>

</body>
</html>

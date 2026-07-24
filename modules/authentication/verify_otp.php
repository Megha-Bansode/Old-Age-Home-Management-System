<?php
/**
 * SevaNest — Verify OTP Page
 * Authentication Module
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP — SevaNest</title>
    <meta name="description" content="Enter the 6-digit verification code sent to your registered email.">

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
    
    <!-- Verify OTP Page Custom CSS -->
    <link href="../../assets/css/verify_otp.css" rel="stylesheet">
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
            <h1 class="auth-card-title">Verify OTP</h1>
            <p class="auth-card-subtitle">We have sent a 6-digit verification code to your registered email address. Please enter it below.</p>
        </div>

        <!-- Alert Container -->
        <div id="alert-container" aria-live="polite"></div>

        <!-- OTP Verification Form -->
        <form id="otp-verification-form" novalidate autocomplete="off">
            <div class="form-group mb-lg">
                <label class="form-label required text-center w-100 mb-md">Enter Verification Code</label>
                <div class="otp-inputs-wrapper">
                    <input type="text" class="form-control otp-digit-input" maxlength="1" pattern="[0-9]*" inputmode="numeric" required aria-label="Digit 1">
                    <input type="text" class="form-control otp-digit-input" maxlength="1" pattern="[0-9]*" inputmode="numeric" required aria-label="Digit 2">
                    <input type="text" class="form-control otp-digit-input" maxlength="1" pattern="[0-9]*" inputmode="numeric" required aria-label="Digit 3">
                    <input type="text" class="form-control otp-digit-input" maxlength="1" pattern="[0-9]*" inputmode="numeric" required aria-label="Digit 4">
                    <input type="text" class="form-control otp-digit-input" maxlength="1" pattern="[0-9]*" inputmode="numeric" required aria-label="Digit 5">
                    <input type="text" class="form-control otp-digit-input" maxlength="1" pattern="[0-9]*" inputmode="numeric" required aria-label="Digit 6">
                </div>
            </div>

            <button type="submit" id="verify-otp-btn" class="btn btn-primary w-100 btn-hover-grow">
                <span class="btn-text">Verify OTP</span>
            </button>
        </form>

        <!-- Resend Section with Countdown Timer -->
        <div class="otp-resend-section mt-md text-center">
            <span class="resend-text">Didn't receive the code?</span>
            <a href="#" id="resend-otp-link" class="resend-link disabled-link">Resend OTP</a>
            <span id="countdown-timer-text" class="timer-text">(30s)</span>
        </div>

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
<!-- Verify OTP logic -->
<script src="../../assets/js/verify_otp.js"></script>

</body>
</html>

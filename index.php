<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to SevaNest - Old Age Home Management System</title>
    
    <!-- Bootstrap 5.3.2 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <!-- SevaNest Global Style Theme -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        body {
            background-color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .landing-card {
            max-width: 650px;
            width: 100%;
            border-radius: 20px;
            padding: 48px;
            background-color: #fff;
            box-shadow: 0 15px 45px rgba(47, 58, 58, 0.08);
            border: 1px solid rgba(107, 144, 128, 0.15);
            text-align: center;
            transition: var(--transition-smooth);
        }
        .landing-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 55px rgba(47, 58, 58, 0.12);
        }
        .logo-badge {
            width: 120px;
            height: 120px;
            background-color: #fff;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 32px;
            margin: 0 auto 24px auto;
            box-shadow: 0 8px 25px rgba(107, 144, 128, 0.2);
            border: 2px solid rgba(107, 144, 128, 0.15);
        }
        .landing-title {
            color: var(--text-color);
            font-weight: 800;
            font-size: 32px;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        .landing-subtitle {
            color: var(--primary-color);
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 24px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .landing-description {
            color: var(--text-color);
            font-size: 15px;
            line-height: 1.6;
            opacity: 0.85;
            margin-bottom: 36px;
        }
        .action-link {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            padding: 14px 32px;
            border-radius: 14px;
            box-shadow: 0 6px 20px rgba(107, 144, 128, 0.25);
        }
        .action-link i {
            transition: var(--transition-smooth);
        }
        .action-link:hover i {
            transform: translateX(4px);
        }
        .support-info {
            margin-top: 40px;
            border-top: 1px solid rgba(0, 0, 0, 0.06);
            padding-top: 24px;
            display: flex;
            justify-content: center;
            gap: 24px;
            font-size: 13px;
            color: var(--text-color);
            opacity: 0.7;
        }
    </style>
</head>
<body>

    <div class="landing-card">
        <div class="logo-badge" style="overflow: hidden;">
            <img src="assets/images/logo.jpg" alt="SevaNest Logo" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        
        <h1 class="landing-title">SevaNest</h1>
        <p class="landing-subtitle">Old Age Home Management System</p>
        
        <p class="landing-description">
            Eldercare administration made simple. Monitor resident wellness, coordinate nurse visits, organize volunteers, record donations, and handle system privileges from our responsive, accessible control panel.
        </p>
        
        <div class="mb-2">
            <a href="modules/super-admin/dashboard/index.php" class="btn btn-primary-custom action-link">
                <span>Enter Super Admin Portal</span>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        
        <div class="support-info">
            <span><i class="bi bi-telephone-fill me-1"></i> +91 9876543210</span>
            <span><i class="bi bi-envelope-fill me-1"></i> contact@sevanest.org</span>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6Rz5YN5g87BH95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL3FHRgQP" crossorigin="anonymous"></script>
    
    <!-- Custom Scripts -->
    <script src="assets/js/main.js"></script>
</body>
</html>

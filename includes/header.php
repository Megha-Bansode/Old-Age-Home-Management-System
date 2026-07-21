<?php
if (!isset($path_to_root)) {
    $path_to_root = "./";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - SevaNest' : 'SevaNest - Old Age Home Management'; ?></title>
    
    <!-- Bootstrap 5.3.2 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <!-- Custom Project Theme CSS -->
    <link rel="stylesheet" href="<?php echo $path_to_root; ?>assets/css/style.css">
    
    <!-- Module-Specific Overrides CSS -->
    <link rel="stylesheet" href="<?php echo $path_to_root; ?>modules/super-admin/assets/css/style.css">
</head>
<body>

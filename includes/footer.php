<?php
if (!isset($path_to_root)) {
    $path_to_root = "./";
}
?>
    <!-- Bootstrap 5 JS Bundle (with Popper) CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6Rz5YN5g87BH95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL3FHRgQP" crossorigin="anonymous"></script>
    
    <!-- Custom Project Theme Scripts -->
    <script src="<?php echo $path_to_root; ?>assets/js/main.js"></script>
    
    <!-- Module-Specific Scripts -->
    <script src="<?php echo $path_to_root; ?>modules/super-admin/assets/js/main.js"></script>
</body>
</html>

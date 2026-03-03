<?php use LunoxHoshizaki\View\View; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Lunox Hoshizaki">
    <title><?php echo htmlspecialchars($title ?? $_ENV['APP_NAME'] ?? 'Lunox Backfire'); ?></title>
    <!-- Google Fonts: Google Sans Flex and Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@8..144,100..1000&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Google Sans Flex', system-ui, -apple-system, sans-serif;
            color: #333;
        }
        .main-content {
            min-height: calc(100vh - 140px);
            padding: 40px 0;
        }
        .gradient-text {
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .material-symbols-outlined {
            vertical-align: middle;
            line-height: 1;
        }
    </style>
</head>
<body>

    <?php View::component('components.navbar'); ?>

    <main class="main-content container">
        <?php View::yield('content'); ?>
    </main>

    <footer class="bg-dark text-light py-4 mt-auto">
        <div class="container text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> <strong><?php echo $_ENV['APP_NAME'] ?? 'Lunox Backfire'; ?></strong> v<?php echo $_ENV['APP_VERSION'] ?? '1.1.0'; ?>. Designed by Lunox Hoshizaki.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

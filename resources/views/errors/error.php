<?php use LunoxHoshizaki\View\View; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $code ?? 'Error'; ?> - <?php echo $_ENV['APP_NAME'] ?? 'Lunox Backfire'; ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@8..144,100..1000&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Google Sans Flex', system-ui, -apple-system, sans-serif;
            color: #333;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            max-width: <?php echo (isset($exception) && ($_ENV['APP_DEBUG'] ?? false) == 'true' && ($code ?? 500) == 500) ? '900px' : '600px'; ?>;
            width: 100%;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            line-height: 1;
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
        }
        .material-symbols-outlined.icon-large {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <?php if (($code ?? 500) == 404): ?>
            <span class="material-symbols-outlined icon-large text-warning">travel_explore</span>
            <div class="error-code">404</div>
            <h2 class="fw-bold mb-3">Page Not Found</h2>
            <p class="text-muted mb-4">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
        <?php elseif (($code ?? 500) == 419): ?>
            <span class="material-symbols-outlined icon-large text-warning">security</span>
            <div class="error-code">419</div>
            <h2 class="fw-bold mb-3">Page Expired</h2>
            <p class="text-muted mb-4">Your session has expired or the CSRF token was invalid. Please try again.</p>
        <?php else: ?>
            <!-- Production View -->
            <span class="material-symbols-outlined icon-large text-danger">error</span>
            <div class="error-code"><?php echo $code ?? 500; ?></div>
            <h2 class="fw-bold mb-3"><?php echo htmlspecialchars($message ?? 'Server Error'); ?></h2>
            <p class="text-muted mb-4">We encountered an unexpected condition that prevented us from fulfilling the request.</p>
        <?php endif; ?>

    </div>
</body>
</html>

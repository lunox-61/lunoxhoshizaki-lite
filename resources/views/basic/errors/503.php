<?php use LunoxHoshizaki\View\View; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 Service Unavailable - <?php echo $_ENV['APP_NAME'] ?? 'Lunox Backfire'; ?></title>
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
            max-width: 600px;
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
            color: #198754;
            margin-bottom: 20px;
        }
        .btn-retry {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <span class="material-symbols-outlined icon-large text-success">handyman</span>
        <div class="error-code">503</div>
        <h2 class="fw-bold mb-3">Service Unavailable</h2>
        <p class="text-muted mb-4">The application is currently down for scheduled maintenance or upgrades. We'll be back online shortly. Thank you for your patience.</p>
        
        <button onclick="window.location.reload();" class="btn btn-primary btn-lg mt-3 d-inline-flex align-items-center gap-2">
            <span class="material-symbols-outlined">refresh</span> Retrieve Status
        </button>
    </div>
</body>
</html>

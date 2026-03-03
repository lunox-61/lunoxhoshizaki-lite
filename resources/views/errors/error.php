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
            <?php if (isset($exception) && ($_ENV['APP_DEBUG'] ?? false) == 'true'): ?>
                <!-- Debug Mode View -->
                <div class="text-start" style="text-align: left;">
                    <div class="mb-4">
                        <span class="badge bg-danger mb-2 px-3 py-2 fs-6">ErrorException</span>
                        <h3 class="fw-bold text-danger" style="word-break: break-word;"><?php echo htmlspecialchars($exception->getMessage()); ?></h3>
                        <p class="text-muted"><span class="material-symbols-outlined fs-6 align-middle">folder</span> <strong><?php echo $exception->getFile(); ?></strong> on line <strong><?php echo $exception->getLine(); ?></strong></p>
                    </div>

                    <?php 
                        // Code Snippet Reading
                        $file = $exception->getFile();
                        $line = $exception->getLine();
                        $codeSnippet = [];
                        if (file_exists($file)) {
                            $lines = file($file);
                            $start = max(0, $line - 6);
                            $end = min(count($lines), $line + 5);
                            for ($i = $start; $i < $end; $i++) {
                                $codeSnippet[$i + 1] = $lines[$i];
                            }
                        }
                    ?>

                    <?php if (!empty($codeSnippet)): ?>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-dark text-white border-0 py-3">
                            <span class="material-symbols-outlined align-middle fs-5 me-2">code</span> Code Snippet
                        </div>
                        <div class="card-body p-0 bg-light" style="overflow-x: auto;">
                            <pre class="m-0 p-3" style="font-size: 0.9rem;"><code><?php foreach ($codeSnippet as $num => $codeLine): ?>
<span class="<?php echo $num === $line ? 'bg-warning text-dark px-2 fw-bold' : 'text-muted'; ?>" style="display:inline-block; min-width:30px;"><?php echo $num; ?></span> <?php echo htmlspecialchars($codeLine); ?><?php endforeach; ?></code></pre>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-light fw-bold">
                            <span class="material-symbols-outlined align-middle fs-5 me-2">list_alt</span> Stack Trace
                        </div>
                        <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                            <ul class="list-group list-group-flush" style="font-size: 0.85rem;">
                                <?php foreach ($exception->getTrace() as $index => $trace): ?>
                                    <li class="list-group-item bg-light text-wrap text-break">
                                        <div class="fw-bold text-primary">
                                            #<?php echo $index; ?> <?php echo htmlspecialchars($trace['file'] ?? 'Unknown File'); ?>:<?php echo $trace['line'] ?? '?'; ?>
                                        </div>
                                        <div class="mt-1 text-muted">
                                            <?php echo htmlspecialchars($trace['class'] ?? ''); ?><?php echo htmlspecialchars($trace['type'] ?? ''); ?><?php echo htmlspecialchars($trace['function'] ?? ''); ?>()
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Production View -->
                <span class="material-symbols-outlined icon-large text-danger">error</span>
                <div class="error-code"><?php echo $code ?? 500; ?></div>
                <h2 class="fw-bold mb-3"><?php echo htmlspecialchars($message ?? 'Server Error'); ?></h2>
                <p class="text-muted mb-4">We encountered an unexpected condition that prevented us from fulfilling the request.</p>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (!(isset($exception) && ($_ENV['APP_DEBUG'] ?? false) == 'true')): ?>
            <a href="/" class="btn btn-primary btn-lg mt-3 d-inline-flex align-items-center gap-2">
                <span class="material-symbols-outlined">home</span> Back to Home
            </a>
        <?php endif; ?>
    </div>
</body>
</html>

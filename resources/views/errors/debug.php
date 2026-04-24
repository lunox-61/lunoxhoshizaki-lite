<?php
$exClass   = isset($exception) ? get_class($exception) : 'Error';
$exMessage = isset($exception) ? htmlspecialchars($exception->getMessage()) : 'Unknown Error';
$exFile    = isset($exception) ? $exception->getFile() : '';
$exLine    = isset($exception) ? $exception->getLine() : 0;
$exCode    = isset($exception) ? $exception->getCode() : ($code ?? 500);
$httpCode  = $code ?? 500;
$appName   = $_ENV['APP_NAME'] ?? 'Lunox Backfire';
$phpVer    = phpversion();
$httpMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$httpProto  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'HTTPS' : 'HTTP';

// Error category badge color
$typeColor = match (true) {
    str_contains($exClass, 'Type')      => '#e67e22',
    str_contains($exClass, 'Parse')     => '#8e44ad',
    str_contains($exClass, 'Runtime')   => '#c0392b',
    str_contains($exClass, 'Logic')     => '#2980b9',
    str_contains($exClass, 'Invalid')   => '#d35400',
    str_contains($exClass, 'PDO')       => '#16a085',
    default                              => '#c0392b',
};

// Method badge color
$methodColor = match ($httpMethod) {
    'GET'    => '#27ae60',
    'POST'   => '#2980b9',
    'PUT'    => '#f39c12',
    'PATCH'  => '#16a085',
    'DELETE' => '#c0392b',
    default  => '#7f8c8d',
};

// Code Snippet
$codeSnippet = [];
if (isset($exception) && file_exists($exFile)) {
    $lines = file($exFile);
    $start = max(0, $exLine - 6);
    $end   = min(count($lines), $exLine + 5);
    for ($i = $start; $i < $end; $i++) {
        $codeSnippet[$i + 1] = $lines[$i];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($exClass); ?> – <?php echo $appName; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #f4f6f9;
            font-family: 'Inter', system-ui, sans-serif;
            color: #1e293b;
            min-height: 100vh;
        }

        /* ── Top info bar ── */
        .info-bar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 10px 40px;
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            font-size: 0.8rem;
            color: #64748b;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .info-bar .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.78rem;
            font-weight: 500;
        }
        .pill .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
        }
        .pill .label { color: #94a3b8; margin-right: 2px; }

        /* ── Main layout ── */
        .debug-wrap {
            max-width: 980px;
            margin: 0 auto;
            padding: 36px 24px 60px;
        }

        /* ── Exception header ── */
        .ex-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 8px;
            padding: 6px 16px;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            margin-bottom: 16px;
        }
        .ex-message {
            font-size: 1.5rem;
            font-weight: 700;
            color: #dc2626;
            line-height: 1.4;
            margin-bottom: 12px;
            word-break: break-word;
        }
        .ex-location {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-size: 0.875rem;
        }
        .ex-location strong { color: #334155; }

        /* ── Divider ── */
        .section-divider {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 28px 0;
        }

        /* ── Section header ── */
        .section-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin-bottom: 14px;
        }
        .section-title .material-symbols-outlined { font-size: 1rem; }

        /* ── Code snippet ── */
        .snippet-wrap {
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            margin-bottom: 28px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .snippet-header {
            background: #f8fafc;
            padding: 10px 18px;
            font-size: 0.78rem;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
        }
        .snippet-pre {
            background: #282c34;
            overflow-x: auto;
            margin: 0;
            padding: 0;
        }
        .snippet-pre code {
            display: block;
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            font-size: 0.875rem;
            line-height: 1.7;
        }
        .code-line {
            display: flex;
            padding: 0 18px;
        }
        .code-line.active-line {
            background: rgba(239, 68, 68, 0.2);
            border-left: 3px solid #ef4444;
        }
        .code-line .ln {
            min-width: 40px;
            text-align: right;
            margin-right: 20px;
            color: #4b5563;
            user-select: none;
            flex-shrink: 0;
        }
        .code-line.active-line .ln { color: #fca5a5; }
        .code-line .lc { color: #abb2bf; flex: 1; white-space: pre; }

        /* ── Stack trace ── */
        .trace-wrap {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            background: #ffffff;
        }
        .trace-item {
            padding: 14px 18px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            gap: 14px;
            align-items: flex-start;
        }
        .trace-item:last-child { border-bottom: none; }
        .trace-num {
            background: #f1f5f9;
            color: #94a3b8;
            font-size: 0.7rem;
            font-weight: 700;
            min-width: 26px;
            height: 26px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .trace-file {
            font-size: 0.825rem;
            color: #2563eb;
            margin-bottom: 3px;
        }
        .trace-file span { color: #d97706; }
        .trace-func {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            color: #64748b;
        }
        .trace-list-inner {
            max-height: 420px;
            overflow-y: auto;
            background: #ffffff;
        }
    </style>
</head>
<body>

    <!-- ── Info bar ── -->
    <div class="info-bar">
        <span class="pill">
            <span class="dot" style="background:<?php echo $methodColor; ?>"></span>
            <span class="label">Method</span>
            <strong style="color:<?php echo $methodColor; ?>"><?php echo $httpMethod; ?></strong>
        </span>
        <span class="pill">
            <span class="material-symbols-outlined" style="font-size:0.9rem;color:#64748b;">link</span>
            <span class="label">URL</span>
            <strong style="color:#e2e8f0;"><?php echo htmlspecialchars($httpProto . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $requestUri); ?></strong>
        </span>
        <span class="pill">
            <span class="material-symbols-outlined" style="font-size:0.9rem;color:#64748b;">http</span>
            <span class="label">Status</span>
            <strong style="color:#f87171;"><?php echo $httpCode; ?></strong>
        </span>
        <span class="pill">
            <span class="material-symbols-outlined" style="font-size:0.9rem;color:#64748b;">developer_mode</span>
            <span class="label">PHP</span>
            <strong style="color:#a78bfa;"><?php echo $phpVer; ?></strong>
        </span>
        <span class="pill">
            <span class="material-symbols-outlined" style="font-size:0.9rem;color:#64748b;">schedule</span>
            <strong><?php echo date('H:i:s'); ?></strong>
        </span>
    </div>

    <!-- ── Main content ── -->
    <div class="debug-wrap">

        <!-- Exception header -->
        <div class="ex-type-badge" style="background:<?php echo $typeColor; ?>1a; border:1px solid <?php echo $typeColor; ?>55; color:<?php echo $typeColor; ?>">
            <span class="material-symbols-outlined" style="font-size:1rem;">bug_report</span>
            <?php echo htmlspecialchars($exClass); ?>
        </div>
        <div class="ex-message"><?php echo $exMessage; ?></div>
        <div class="ex-location">
            <span class="material-symbols-outlined" style="font-size:1rem;">folder_open</span>
            <span><strong><?php echo htmlspecialchars($exFile); ?></strong> &nbsp;·&nbsp; line <strong><?php echo $exLine; ?></strong></span>
        </div>

        <hr class="section-divider">

        <!-- Code Snippet -->
        <?php if (!empty($codeSnippet)): ?>
        <div class="section-title">
            <span class="material-symbols-outlined">code</span> Code Snippet
        </div>
        <div class="snippet-wrap">
            <div class="snippet-header">
                <span><?php echo htmlspecialchars(basename($exFile)); ?></span>
                <span>Line <?php echo $exLine; ?></span>
            </div>
            <div class="snippet-pre">
                <code>
                    <?php foreach ($codeSnippet as $num => $codeLine): ?>
                    <div class="code-line <?php echo $num === $exLine ? 'active-line' : ''; ?>">
                        <span class="ln"><?php echo $num; ?></span>
                        <span class="lc"><?php echo htmlspecialchars(rtrim($codeLine, "\r\n")); ?></span>
                    </div>
                    <?php endforeach; ?>
                </code>
            </div>
        </div>
        <?php endif; ?>

        <!-- Stack Trace -->
        <div class="section-title">
            <span class="material-symbols-outlined">list_alt</span> Stack Trace
        </div>
        <div class="trace-wrap">
            <div class="trace-list-inner">
                <?php if (isset($exception)): ?>
                    <?php foreach ($exception->getTrace() as $index => $trace): ?>
                    <div class="trace-item">
                        <div class="trace-num">#<?php echo $index; ?></div>
                        <div>
                            <div class="trace-file">
                                <?php echo htmlspecialchars($trace['file'] ?? 'Unknown File'); ?>
                                <?php if (isset($trace['line'])): ?>
                                    &nbsp;·&nbsp; <span>line <?php echo $trace['line']; ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="trace-func">
                                <?php echo htmlspecialchars(($trace['class'] ?? '') . ($trace['type'] ?? '') . ($trace['function'] ?? '') . '()'); ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</body>
</html>

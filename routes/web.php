<?php

use LunoxHoshizaki\Routing\Router;
use App\Controllers\HomeController;
use LunoxHoshizaki\Security\CsrfMiddleware;

// Web Routes
Router::get('/', [HomeController::class, 'index'])->name('home');
Router::get('/about', [HomeController::class, 'about'])->name('about');

// Documentation Route Group
Router::prefix('/docs')->group(function () {
    Router::get('/', [\App\Controllers\DocsController::class, 'index'])->name('docs.index');
    Router::get('/{page}', [\App\Controllers\DocsController::class, 'show'])->name('docs.show');
});

// Protected POST route with CSRF
Router::post('/submit', [HomeController::class, 'formSubmit'])
    ->middleware([CsrfMiddleware::class])
    ->name('form.submit');

// Route to simulate a 500 Internal Server Error
Router::get('/broken-route', function () {
    throw new Exception("Simulated Server Exception for testing!");
})->name('broken');

// API Route Example
Router::get('/api/users/{id}', function ($request, $id) {
    return [
        'id' => $id,
        'name' => 'John Doe',
        'framework' => 'LunoxHoshizaki Lite'
    ];
});

// Auto-reload polling endpoint for development mode
Router::get('/__dev/poll', function () {
    // Generate a hash based on the modification times of important directories
    $directories = [
        __DIR__ . '/../app/Controllers',
        __DIR__ . '/../resources/views',
        __DIR__ . '/../resources/views/basic/layouts',
        __DIR__ . '/../resources/views/basic/components',
        __DIR__ . '/../src'
    ];

    $hashStr = '';
    foreach ($directories as $dir) {
        if (!is_dir($dir))
            continue;
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($files as $file) {
            if ($file->isFile()) {
                $hashStr .= $file->getMTime();
            }
        }
    }

    return ['hash' => md5($hashStr)];
});

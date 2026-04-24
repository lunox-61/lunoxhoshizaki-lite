<?php

use Exception;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use LunoxHoshizaki\Routing\Router;
use LunoxHoshizaki\Security\CsrfMiddleware;
use App\Controllers\HomeController;
use App\Controllers\DocsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/

Router::get('/', [HomeController::class, 'index'])->name('home');
Router::get('/about', [HomeController::class, 'about'])->name('about');
Router::get('/error-side', [HomeController::class, 'errorSide'])->name('errorSide');

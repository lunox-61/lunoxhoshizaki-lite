<?php

namespace App\Controllers;

use LunoxHoshizaki\Http\Request;

class DocsController
{
    public function index(Request $request)
    {
        return $this->show($request, 'installation');
    }

    public function show(Request $request, $page)
    {
        // Allowed documentation pages
        $pages = [
            'installation' => 'Installation',
            'routing' => 'Routing',
            'middleware' => 'Middleware',
            'controllers' => 'Controllers',
            'views' => 'Views',
            'validation' => 'Validation',
            'authentication' => 'Authentication',
            'helpers' => 'Session & Helpers',
            'database' => 'Database & Models',
            'migrations' => 'Database Migrations',
            'orm' => 'ORM Relationships',
            'storage' => 'File Storage',
            'cli' => 'Custom CLI Commands',
            'mail' => 'Mailer System',
            'cache' => 'Cache System',
            'events' => 'Events & Listeners',
            'security' => 'Feature Security & DDOS',
            'csrf' => 'CSRF Protection',
            'request-response' => 'Request & Response',
            'errors' => 'Error Handling',
            'env' => 'Environment Configuration',
            'artisan' => 'Backfire CLI'
        ];

        if (!array_key_exists($page, $pages)) {
            throw new \Exception("Documentation page not found.", 404);
        }

        return \LunoxHoshizaki\View\View::make('basic.docs.page', [
            'title' => $pages[$page] . ' - Documentation',
            'activeLine' => $page,
            'pages' => $pages
        ]);
    }
}
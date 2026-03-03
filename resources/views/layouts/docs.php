<?php use LunoxHoshizaki\View\View; ?>
<?php View::extends('layouts.app'); ?>

<?php View::section('content'); ?>
<style>
    /* Docs specific styling bridging standard layout */
    .docs-sidebar {
        /* In an actual implementation this might need an offset for a fixed navbar,
           but our navbar currently is static. We'll add some top padding. */
        padding-top: 1rem;
    }
    
    @media (min-width: 768px) {
        .docs-sidebar {
            position: sticky;
            top: 20px;
            height: calc(100vh - 40px);
            overflow-y: auto;
        }
    }

    .docs-nav-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #343a40;
        margin-bottom: 0.5rem;
        margin-top: 1.5rem;
    }
    .docs-nav-title:first-child {
        margin-top: 0;
    }

    .docs-nav-link {
        color: #6c757d;
        text-decoration: none;
        display: block;
        padding: 0.4rem 0;
        border-left: 2px solid transparent;
        padding-left: 1rem;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    
    .docs-nav-link:hover {
        color: #212529;
        border-left-color: #dee2e6;
    }
    
    .docs-nav-link.active {
        color: #0d6efd;
        font-weight: 600;
        border-left-color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    /* Content Formatting */
    .docs-content {
        padding-top: 1rem;
        padding-bottom: 4rem;
    }
    .docs-content h1 { 
        font-weight: 800; 
        margin-bottom: 1.5rem; 
        font-size: 2.5rem;
        color: #212529;
    }
    .docs-content h2 { 
        font-weight: 700; 
        margin-top: 2.5rem; 
        margin-bottom: 1rem; 
        border-bottom: 1px solid #dee2e6; 
        padding-bottom: 0.5rem; 
        font-size: 1.75rem;
    }
    .docs-content h3 {
        font-weight: 600;
        margin-top: 2rem;
        margin-bottom: 0.75rem;
        font-size: 1.25rem;
    }
    .docs-content p { 
        line-height: 1.7; 
        color: #495057;
        margin-bottom: 1.25rem;
    }
    .docs-content ul {
        color: #495057;
        margin-bottom: 1.25rem;
    }
    .docs-content li {
        margin-bottom: 0.5rem;
    }
    .docs-content pre { 
        background: #212529 !important; 
        color: #f8f9fa !important; 
        padding: 1.25rem; 
        border-radius: 0.5rem;
        margin-top: 1rem;
        margin-bottom: 1.5rem;
    }
    .docs-content code:not(pre code) { 
        background-color: #e9ecef; 
        color: #d63384; 
        padding: 0.2rem 0.4rem; 
        border-radius: 0.25rem; 
        font-size: 0.875em; 
    }
</style>

<div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 d-none d-md-block docs-sidebar border-end pe-3">
        <nav class="nav flex-column mb-5">
            <h4 class="docs-nav-title">Prologue</h4>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'installation' ? 'active' : ''; ?>" href="/docs/installation">Installation</a>

            <h4 class="docs-nav-title">The Basics</h4>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'routing' ? 'active' : ''; ?>" href="/docs/routing">Routing</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'middleware' ? 'active' : ''; ?>" href="/docs/middleware">Middleware</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'controllers' ? 'active' : ''; ?>" href="/docs/controllers">Controllers</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'views' ? 'active' : ''; ?>" href="/docs/views">Views</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'validation' ? 'active' : ''; ?>" href="/docs/validation">Validation</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'authentication' ? 'active' : ''; ?>" href="/docs/authentication">Authentication</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'helpers' ? 'active' : ''; ?>" href="/docs/helpers">Helpers & Session</a>

            <h4 class="docs-nav-title">Database</h4>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'database' ? 'active' : ''; ?>" href="/docs/database">Models & Active Record</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'migrations' ? 'active' : ''; ?>" href="/docs/migrations">Database Migrations</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'orm' ? 'active' : ''; ?>" href="/docs/orm">ORM Relationships</a>

            <h4 class="docs-nav-title">Services</h4>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'cache' ? 'active' : ''; ?>" href="/docs/cache">Cache System</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'events' ? 'active' : ''; ?>" href="/docs/events">Events & Listeners</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'storage' ? 'active' : ''; ?>" href="/docs/storage">File Storage</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'mail' ? 'active' : ''; ?>" href="/docs/mail">Mailer System</a>

            <h4 class="docs-nav-title">Security</h4>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'security' ? 'active' : ''; ?>" href="/docs/security">Feature Security & DDOS</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'csrf' ? 'active' : ''; ?>" href="/docs/csrf">CSRF Protection</a>

            <h4 class="docs-nav-title">Digging Deeper</h4>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'request-response' ? 'active' : ''; ?>" href="/docs/request-response">Request & Response</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'errors' ? 'active' : ''; ?>" href="/docs/errors">Error Handling</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'env' ? 'active' : ''; ?>" href="/docs/env">Environment Configuration</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'artisan' ? 'active' : ''; ?>" href="/docs/artisan">Backfire Console</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'cli' ? 'active' : ''; ?>" href="/docs/cli">Custom CLI Commands</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 col-lg-8 docs-content ps-md-5">
        <?php View::yield('docs-content'); ?>
    </div>
    
    <!-- Optional wide screen TOC skeleton -->
    <div class="col-lg-2 d-none d-lg-block">
        <!-- Future TOC placeholder -->
    </div>
</div>

<?php View::endsection(); ?>

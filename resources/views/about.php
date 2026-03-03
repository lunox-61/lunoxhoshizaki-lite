<?php use LunoxHoshizaki\View\View; ?>
<?php View::extends('layouts.app'); ?>

<?php View::section('content'); ?>
    <div class="text-center py-5">
        <h1 class="display-5 fw-bold mb-4 d-flex align-items-center justify-content-center gap-3">
            <span class="material-symbols-outlined" style="font-size: 3rem;">info</span>
            <?php echo htmlspecialchars($title ?? 'About'); ?>
        </h1>
        <p class="lead text-muted max-w-2xl mx-auto">
            This framework was built to provide an advanced yet straightforward MVC architecture.
            It leverages robust features like PSR-4 autoloading, Active Record Models, Middleware,
            and an intuitive View compiling system that resembles Blade templating.
        </p>
        <div class="mt-5 row text-start justify-content-center">
            <div class="col-md-4">
                <div class="p-4 bg-white rounded shadow-sm h-100 border">
                    <span class="material-symbols-outlined text-primary mb-2 fs-1">route</span>
                    <h5 class="fw-bold text-primary">Advanced Routing</h5>
                    <p class="text-muted small">Supports dynamic URL segments, middleware pipelines, and easy controller binding.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded shadow-sm h-100 border">
                    <span class="material-symbols-outlined text-success mb-2 fs-1">lock</span>
                    <h5 class="fw-bold text-success">Security Built-in</h5>
                    <p class="text-muted small">Out of the box protection with CSRF validation and flexible CORS handling middleware.</p>
                </div>
            </div>
        </div>
    </div>
<?php View::endsection(); ?>

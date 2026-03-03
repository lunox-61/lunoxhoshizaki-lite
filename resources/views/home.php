<?php use LunoxHoshizaki\View\View; ?>
<?php View::extends('layouts.app'); ?>

<?php View::section('content'); ?>
    <div class="row align-items-center mb-5">
        <div class="col-lg-6">
            <h1 class="display-4 fw-bold mb-3"><?php echo htmlspecialchars($title ?? 'Home'); ?></h1>
            <p class="lead text-muted mb-4"><?php echo htmlspecialchars($message ?? 'Welcome!'); ?></p>
            <div class="d-flex gap-3">
                <a href="/docs" class="btn btn-primary btn-lg px-4 shadow-sm d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined">bolt</span> Get Started
                </a>
            </div>
        </div>
        <div class="col-lg-6 d-none d-lg-block text-center">
            <div class="p-5 bg-white rounded-4 shadow-sm border">
                <span class="material-symbols-outlined text-primary mb-3" style="font-size: 4rem;">speed</span>
                <h3 class="gradient-text fw-bold">Built for Speed</h3>
                <p class="text-muted mt-3">Experience modern PHP development with Laravel-like elegance, packed in a lightweight footprint.</p>
            </div>
        </div>
    </div>

    <hr class="my-5">

    <div class="row mt-5">
        <div class="col-md-6 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 pt-4 pb-0 d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined text-success">security</span>
                    <h4 class="card-title fw-bold mb-0">Test CSRF Security</h4>
                </div>
                <div class="card-body p-4">
                    <form action="/submit" method="POST">
                        <?php View::csrfField(); ?>
                        <div class="mb-3">
                            <label for="name" class="form-label text-muted fw-semibold">Your Name</label>
                            <input type="text" class="form-control form-control-lg" id="name" name="name" placeholder="Enter your name" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100 btn-lg shadow-sm d-flex justify-content-center align-items-center gap-2">
                            <span class="material-symbols-outlined">send</span> Test Form Submission
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6 mx-auto mt-4 mt-md-0">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0 d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined text-danger">bug_report</span>
                    <h4 class="card-title fw-bold mb-0">Test Error Pages</h4>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-4">Click the buttons below to see how the framework handles different HTTP error response codes.</p>
                    <div class="d-grid gap-3">
                        <a href="/this-url-is-a-404" class="btn btn-outline-warning btn-lg d-flex align-items-center justify-content-center gap-2">
                            <span class="material-symbols-outlined">travel_explore</span> Test 404 (Not Found)
                        </a>
                        
                        <!-- Form without CSRF token to trigger 419 -->
                        <form action="/submit" method="POST" class="m-0">
                            <button type="submit" class="btn btn-outline-secondary btn-lg w-100 d-flex justify-content-center align-items-center gap-2">
                                <span class="material-symbols-outlined">timer_off</span> Test 419 (Expired/CSRF)
                            </button>
                        </form>

                        <a href="/broken-route" class="btn btn-outline-danger btn-lg d-flex align-items-center justify-content-center gap-2">
                            <span class="material-symbols-outlined">error</span> Test 500 (Server Error)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php View::endsection(); ?>

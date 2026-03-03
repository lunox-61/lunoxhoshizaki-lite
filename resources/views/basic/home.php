<?php use LunoxHoshizaki\View\View; ?>
<?php View:: extends('basic.layouts.app'); ?>

<?php View::section('content'); ?>
<div class="d-flex flex-column justify-content-center align-items-center text-center py-5 my-5">
    <span class="material-symbols-outlined text-primary mb-4" style="font-size: 6rem;">rocket_launch</span>
    <h1 class="display-3 fw-bold mb-3"><?php echo htmlspecialchars($title ?? 'Selamat Datang!'); ?></h1>
    <p class="lead text-muted mb-5 max-w-2xl mx-auto fs-4">
        Framework PHP MVC yang simpel dan ringan, cocok banget buat belajar dan bikin aplikasi web tanpa ribet.
    </p>
    <a href=" /docs"
        class="btn btn-primary btn-lg px-5 py-3 shadow-lg rounded-pill d-inline-flex align-items-center gap-2 fs-5">
        <span class="material-symbols-outlined">bolt</span> Mulai Sekarang
    </a>
</div>
<?php View::endsection(); ?>
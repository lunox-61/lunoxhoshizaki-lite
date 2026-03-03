<?php use LunoxHoshizaki\View\View; ?>
<?php View:: extends('basic.layouts.app'); ?>

<?php View::section('content'); ?>
<div class="text-center py-5">
    <h1 class="display-5 fw-bold mb-4 d-flex align-items-center justify-content-center gap-3">
        <span class="material-symbols-outlined" style="font-size: 3rem;">info</span>
        <?php echo htmlspecialchars($title ?? 'Tentang Kami'); ?>
    </h1>
    <p class="lead text-muted max-w-2xl mx-auto">
        Framework ini tuh proyek MVC ringan yang dibikin fokus buat belajar konsep inti PHP modern. Alih-alih pakai
        abstraksi ribet yang susah di-maintain, di sini semuanya dibikin simpel dan gampang dipahamin.
    </p>
    <div class="mt-5 row text-start justify-content-center">
        <div class="col-md-4 mb-4">
            <div class="p-4 bg-white rounded shadow-sm h-100 border">
                <span class="material-symbols-outlined text-primary mb-2 fs-1">route</span>
                <h5 class="fw-bold text-primary">Routing Simpel</h5>
                <p class="text-muted small">Fitur routing dasar buat nyambungin URL langsung ke method controller atau
                    closure dengan gampang dan santai.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="p-4 bg-white rounded shadow-sm h-100 border">
                <span class="material-symbols-outlined text-success mb-2 fs-1">code_blocks</span>
                <h5 class="fw-bold text-success">Arsitektur MVC Dasar</h5>
                <p class="text-muted small">Punya struktur Model-View-Controller yang lurus-lurus aja buat ngebantu
                    pisahin dan ngerapiihin logika aplikasimu.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="p-4 bg-white rounded shadow-sm h-100 border">
                <span class="material-symbols-outlined text-warning mb-2 fs-1">html</span>
                <h5 class="fw-bold text-warning">Templating View Sederhana</h5>
                <p class="text-muted small">Udah disediain mesin bahasa template dasar ala Blade buat bikin HTML dinamis
                    jadi lebih enak diliat dan bersih.</p>
            </div>
        </div>
    </div>
</div>
<?php View::endsection(); ?>
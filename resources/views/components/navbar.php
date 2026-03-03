<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="/">
            <span class="material-symbols-outlined text-primary">rocket_launch</span>
            <?php echo $_ENV['APP_NAME'] ?? 'Lunox Backfire'; ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-1" href="/"><span class="material-symbols-outlined fs-5">home</span> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-1" href="/about"><span class="material-symbols-outlined fs-5">info</span> About</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

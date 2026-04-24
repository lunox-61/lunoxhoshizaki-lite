# Backup of basic views

## about.php

```php
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
```

## docs/page.php

```php
<?php use LunoxHoshizaki\View\View; ?>
<?php View:: extends('basic.layouts.docs'); ?>

<?php View::section('docs-content'); ?>

<?php if ($activeLine === 'installation'): ?>
    <h1>Installation & Architecture</h1>
    <p>Selamat datang di Lunox Backfire. Ini adalah framework PHP yang didesain agar ringan dan cepat, namun tetap memberikan alat dan struktur yang memudahkan pengembangan aplikasi web.</p>

    <h2>Cara Instal Framework</h2>
    <p>Untuk memulai, ikuti langkah-langkah instalasi awal dari repositori GitHub <b>lunoxhoshizaki-lite</b> berikut:
    </p>

    <p><b>1. Clone Repository</b></p>
    <p>Jika Anda ingin nama direktori <i>project</i> berbeda, tambahkan namanya di akhir perintah <code>git clone</code>:</p>
    <pre><code># Format: git clone [URL_REPO] [NAMA_FOLDER_KAMU]
    git clone https://github.com/lunox-61/lunoxhoshizaki-lite.git my-app
    cd my-app</code></pre>

    <p><b>2. Install Dependencies</b></p>
    <p>Jalankan perintah Composer untuk mengunduh seluruh pustaka pendukung:
    </p>
    <pre><code>composer install</code></pre>

    <p><b>3. Dump Autoload</b></p>
    <p>Untuk memastikan semua <i>class</i> PHP dapat dimuat dengan baik, jalankan:</p>
    <pre><code>composer dump-autoload -o</code></pre>

    <p><b>4. Konfigurasi Environment</b></p>
    <p>Salin file konfigurasi contoh ke `.env` agar sistem dapat membaca pengaturannya:</p>
    <pre><code>cp .env.example .env</code></pre>

    <h2>Syarat & Kebutuhan (Requirements)</h2>
    <p>Pastikan sistem Anda sudah memenuhi persyaratan berikut:</p>
    <ul>
        <li>PHP >= 8.1</li>
        <li>Ekstensi PDO (PHP Data Objects)</li>
        <li>Ekstensi OpenSSL</li>
        <li>Database SQL (MySQL / SQLite / MariaDB)</li>
    </ul>

    <h2>Struktur Folder Inti</h2>
    <p>Berikut adalah komponen-komponen utama dalam struktur aplikasi:</p>
    <ul>
        <li><b><code>app/</code></b> : Inti aplikasi Anda, memuat Models, Controllers, dan Events.</li>
        <li><b><code>database/</code></b> : Direktori untuk file Migrations dan Seeders.</li>
        <li><b><code>public/</code></b> : Pintu masuk aplikasi (`index.php`) dan tempat penyimpanan statik (CSS, JS, Images).</li>
        <li><b><code>resources/views/</code></b> : File-file sistem templat tampilan (HTML/UI).</li>
        <li><b><code>routes/</code></b> : Tempat mendaftarkan rute aplikasi (<code>web.php</code> atau <code>api.php</code>).</li>
        <li><b><code>src/</code></b> : Kode pondasi dari framework itu sendiri.</li>
    </ul>

    <h2>Menjalankan Development Server</h2>
    <p>Anda dapat menjalankan server pengembangan internal bawaan PHP melalui perintah terminal ini:</p>
    <pre><code>php backfire serve</code></pre>
    <p>Aplikasi Anda dapat langsung diakses di <code>http://localhost:8000</code>.</p>

<?php elseif ($activeLine === 'routing'): ?>
    <h1>Routing (Alur URL)</h1>
    <p>Routing mendefinisikan tautan (URL) yang diizinkan untuk diakses pada aplikasi beserta proses yang akan dijalankan ketika tautan tersebut dituju.</p>

    <h2>Basic Web Routing</h2>
    <p>Semua URL utama web disimpan di file <code>routes/web.php</code>. Berikut adalah contoh sederhana pengembalian teks sederhana:</p>
    <pre><code>use LunoxHoshizaki\Routing\Router;

    Router::get('/halo', function () {
        return 'Halo Dunia! Welcome!';
    });</code></pre>

    <h2>Routing dengan Parameter</h2>
    <p>Untuk membuat URL dinamis yang menangkap data variabel, Anda bisa mendefinisikan parameter di antara kurung kurawal <code>{ }</code>:</p>
    <pre><code>Router::get('/user/{id}', function ($request, $id) {
        return 'Melihat Profil Pengguna dengan ID: ' . $id;
    });</code></pre>

    <h2>HTTP Methods (POST, PUT, DELETE)</h2>
    <p>Untuk menerima *request* manipulasi data dari form atau API, tersedia pendaftaran route untuk metode HTTP lainnya:</p>
    <pre><code>Router::post('/user/simpan', [UserController::class, 'store']);
    Router::put('/user/{id}/update', [UserController::class, 'update']);
    Router::delete('/user/{id}/hapus', [UserController::class, 'destroy']);</code></pre>

    <h2>Route Grouping & Middleware</h2>
    <p>Route dapat dikelompokkan bersama untuk berbagi konfigurasi perlindungan *middleware* atau *prefix* tanpa duplikasi kode:</p>
    <pre><code>Router::prefix('/admin')
        ->middleware([AuthMiddleware::class])
        ->group(function () {
            Router::get('/dashboard', [AdminController::class, 'index']); // Jadinya: /admin/dashboard
            Router::get('/setting', [AdminController::class, 'setting']); // Jadinya: /admin/setting
    });</code></pre>

    <h2>Named Routes (v2.0)</h2>
    <p>Anda bisa menetapkan alias `->name()` pada suatu rute sehingga dapat lebih efisien ketika dipanggil fungsinya menggunakan *helper* `route()`:</p>
    <pre><code>Router::get('/auth/login-karyawan-baru', [AuthController::class, 'login'])->name('login');

    // Menghasilkan URL relatif:
    echo route('login'); // Output: /auth/login-karyawan-baru</code></pre>

<?php elseif ($activeLine === 'middleware'): ?>
    <h1>Middleware</h1>
    <p><i>Middleware</i> berfungsi sebagai filter yang memproses setiap <i>request</i> yang masuk sebelum mencapai logika aplikasi inti. Contohnya, middleware autentikasi akan memeriksa status login pengguna.</p>

    <h2>Membuat Middleware Baru</h2>
    <p>Kelas middleware dapat digenerate secara otomatis menggunakan perintah CLI:</p>
    <pre><code>php backfire make:middleware CheckAge</code></pre>
    <p>File ini akan dibuat pada <code>app/Http/Middleware</code> atau <code>app/Middleware</code>.</p>

    <p>Di dalam middleware, Anda dapat mendefinisikan logika kondisional pada metode `handle`:</p>
    <pre><code>namespace App\Middleware;

    use LunoxHoshizaki\Http\Request;

    class CheckAge
    {
        public function handle(Request $request, \Closure $next)
        {
            if ($request->input('age') < 18) {
                return redirect('/home');
            }

            return $next($request);
        }
    }</code></pre>

    <h2>Mendaftarkan Middleware ke Route</h2>
    <p>Terapkan kelas Middleware tersebut ke *route* di dalam `routes/web.php` atau `routes/api.php`.</p>
    <pre><code>use App\Middleware\CheckAge;

    // Spesifik pada satu rute
    Router::get('/khusus-dewasa', function () {
        return 'Selamat datang!';
    })->middleware(CheckAge::class);

    // Diimplementasikan pada grup rute tertentu
    Router::prefix('/admin')->middleware([CheckAge::class])->group(function () {
        Router::get('/dashboard', [AdminController::class, 'index']);
    });</code></pre>

<?php elseif ($activeLine === 'controllers'): ?>
    <h1>Controllers</h1>
    <p><b>Controllers</b> mengelompokkan logika penanganan permintaan HTTP yang terkait ke dalam sebuah kelas. Pendekatan ini menjaga alur logika agar terpisah dari pengaturan file <code>web.php</code>.</p>

    <h2>Membuat Controller</h2>
    <p>Gunakan perintah artisan *make*:</p>
    <pre><code>php backfire make:controller UserController</code></pre>
    <p>Ini membuat file persiapan di <code>app/Controllers/UserController.php</code>.</p>

    <h2>Struktur Dasar Controller</h2>
    <p>Controller menerima iterasi dari object Request yang berisi data masukan dan merangkai balasan berupa Tampilan *View* (HTML) atau Respon JSON.</p>
    <pre><code>namespace App\Controllers;

    use LunoxHoshizaki\Http\Request;
    use LunoxHoshizaki\View\View;
    use LunoxHoshizaki\Http\Response;

    class UserController
    {
        // Menampilkan file View HTML User Profile
        public function show(Request $request, $id)
        {
            return View::make('user.profile', ['id' => $id]);
        }

        // Contoh respons format JSON untuk arsitektur API
        public function getJson(Request $request)
        {
            $response = new Response();
            $response->setContent(json_encode(['status' => 'success']));
            $response->setHeader('Content-Type', 'application/json');
            return $response;
        }
    }</code></pre>

    <h2>Menyambungkan Controller ke Route</h2>
    <p>Metode pada Controller dipanggil dengan pendekatan berbasis *Array*:
    </p>
    <pre><code>Router::get('/user/{id}', [UserController::class, 'show']);</code></pre>

<?php elseif ($activeLine === 'views'): ?>
    <h1>Views (Halaman Tampilan)</h1>
    <p><i>Views</i> berguna untuk merender antarmuka pengguna, memisahkan lapisan presentasi (HTML) dari logika bisnis (Controller/Models). File Views dipusatkan pada direktori <code>resources/views</code>.</p>

    <h2>Merender Views</h2>
    <p>Tampilan dimuat melalui class bawaan <code>View::make</code>, ekstensi file <code>.php</code> tidak perlu disertakan.
    </p>
    <pre><code>// Akan mereferensikan file resources/views/greeting.php
    return View::make('greeting', ['nama' => 'Budi']);</code></pre>
    <p>Variabel sisipan dapat dimanfaatkan di dalam `greeting.php` (Catatan: gunakan helper <code>e()</code> untuk mitigasi XSS):
    </p>
    <pre><code>&lt;h1&gt;Halo, &lt;?= e($nama) ?&gt;!&lt;/h1&gt;</code></pre>

    <h2>Layouts (Templating System)</h2>
    <p>Fitur <i>Template Inheritance</i> memudahkan Anda mereusibilitas bagian umum antarmuka (misal *Header* dan *Navigasi*).</p>

    <p><b>1. Pembuatan Master Template (Di <code>resources/views/layouts/app.php</code>):</b></p>
    <pre><code>&lt;html&gt;
    &lt;head&gt;&lt;title&gt;Aplikasi Default&lt;/title&gt;&lt;/head&gt;
    &lt;body&gt;
        &lt;!-- Titik pemanggilan area konten dinamis --&gt;
        &lt;?= $this-&gt;yieldContent('content') ?&gt;
    &lt;/body&gt;
    &lt;/html&gt;</code></pre>

    <p><b>2. Mengimplementasikan Layout di View Utama:</b></p>
    <pre><code>&lt;?php View::extends('layouts.app'); ?&gt;

    &lt;?php View::section('content'); ?&gt;
        &lt;h1&gt;Isi konten eksklusif halaman ini.&lt;/h1&gt;
    &lt;?php View::endsection(); ?&gt;</code></pre>

<?php elseif ($activeLine === 'database'): ?>
    <h1>Database & Models</h1>
    <p>Lunox Backfire menyertakan pola desain dasar perangkat lunak <i>ActiveRecord</i> yang menyederhanakan cara Anda mengoperasikan baris basis data layaknya sebuah objek PHP biasa.</p>

    <h2>Konfigurasi Database</h2>
    <p>Detail koneksi disesuaikan ke dalam file *environment* <code>.env</code> konfigurasi Anda:</p>
    <pre><code>DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=db_name
    DB_USERNAME=root
    DB_PASSWORD=password</code></pre>

    <h2>Pembuatan Model</h2>
    <p>Perwakilan skema entitas data didukung melalui kelas <b>Model</b>. Gunakan instruksi CLI ini:</p>
    <pre><code>php backfire make:model Product</code></pre>
    <p>Konvensi Backfire mendeteksi bentuk jamak dari nama Model sebagai target *Tabel database*-nya (misal: `Product` memetakan langsung ke struktur relasi tabel <code>products</code>).</p>

    <h2>Operasi Dasar CRUD (Nambah, Baca, Edit, Hapus)</h2>
    <p>Siklus pengolahan data standar dapat dilakukan tanpa pengetikan SQL manual:</p>
    <pre><code>// CREATE: Menyisipkan Data Baru
    $produk = new Product();
    $produk->name = 'Buku Panduan';
    $produk->price = 150000;
    $produk->save();

    // READ: Kueri Spesifik
    $satuProduk = Product::find(1); 
    $semuaProduk = Product::where('price', '>', 50000)->get();

    // UPDATE: Memperbaharui record yang diseleksi (Via instances)
    $produkLama = Product::find(1);
    $produkLama->price = 100000;
    $produkLama->save();
    
    // UPDATE CEPAT (Mengubah Nilai via Mass Assignment)
    Product::where('category', 'book')->update(['discount' => 10]);

    // DELETE: Penghapusan basis data absolut atau sementara (SoftDeletes)
    $produkHapus = Product::find(2);
    $produkHapus->delete();</code></pre>

    <h2>Query Builder Lanjutan</h2>
    <p>Objek Query Model menyokong *sorting*, *limit*, maupun *Paginasi* langsung untuk pengolahan komprehensif:</p>
    <pre><code>// Kueri yang memuat fungsi-fungsi kondisional lebih rinci (whereIn, whereNull, leftJoin)
    $terlaris = Product::query()
        ->leftJoin('categories', 'products.cat_id', '=', 'categories.id')
        ->whereIn('tags', ['book', 'pen', 'paper'])
        ->whereNull('deleted_at')
        ->orderBy('sold_count DESC')
        ->limit(10)
        ->get();

    // Integrasi pemecahan kumpulan Array ke UI Halaman terputus (Paginasi)
    $semuaProduk = Product::paginate(15);
    </code></pre>

    <p>Fungsi relasi pemecahan Paginasi itu diformulasikan langsung sebagai navigasi HTML (Bootstrap compatible):</p>
    <pre><code>&lt;!-- Mencetak markup blok navigasi (<< 1 2 3 >>) otomatis --&gt;
    &lt;?= $semuaProduk-&gt;links() ?&gt;</code></pre>

<?php elseif ($activeLine === 'validation'): ?>
    <h1>Validasi</h1>
    <p>Prosedur memvalidasi permintaan yang dikirimkan oleh pengguna sangat dianjurkan untuk mendeteksi anomali pada masukan <i>form request</i>.
    </p>

    <h2>Validasi Objek Controller (v2.0)</h2>
    <p>Atribut persyaratan diimplementasikan secara deskriptif untuk *Request object* meliputi verifikasi eksistensi isian, batasan String hingga keunikan format Tipe data:</p>
    <pre><code>public function store(Request $request)
    {
        // Validasi mencegah aliran di eksekusi lebih jauh dan memunculkan exception apabila nilai tak cocok
        $validated = $request->validate([
            'title'     => 'required|max:255',
            'body'      => 'required',
            'email'     => 'required|email|unique:users,email', // Cek duplikasi di struktur tabel
            'password'  => 'required|min:8',
            'is_active' => 'boolean', // Identifikasi validasi format tipe biner
            'website'   => 'url'
        ]);

        // Aliran akan melewati bagian ini hanya setelah rules disetujui.
        $post = new Post();
        $post->title = $validated['title'];
        $post->save();

        return redirect('/posts');
    }</code></pre>

    <h2>Informasi Respon Error di View</h2>
    <p>Jika pengguna melakukan kesalahan saat submisi form, *Error bag log* dicatat sekilas pada memori sesi. Helper bawaan
        <code>errors()</code> dan <code>old()</code> disediakan untuk interaksi UI yang layak (misal tidak mengosongkan nilai yang pernah didikte pengguna):
    </p>
    <pre><code>&lt;form action="/simpan" method="POST"&gt;
        &lt;!-- Token keamanan minimal pelindung Form --&gt;
        &lt;?= csrf_field() ?&gt;

        &lt;label&gt;Judul:&lt;/label&gt;
        &lt;input type="text" name="title" value="&lt;?= e(old('title')) ?&gt;"&gt;

        &lt;!-- Pemeriksaan logistik error 'title' apabila validasi dikembalikan sistem --&gt;
        &lt;?php if ($error = errors('title')): ?&gt;
            &lt;div style="color:red;"&gt;&lt;?= $error ?&gt;&lt;/div&gt;
        &lt;?php endif; ?&gt;

        &lt;button type="submit"&gt;Kirim Data&lt;/button&gt;
    &lt;/form&gt;</code></pre>
    
    <h2>Ekstraksi Validasi Form Khusus</h2>
    <p>Untuk menyingkirkan logika berat di *Controller*, tersedia berkas kelas abstrak *FormRequest* dengan memanggil `php backfire make:request StoreDataRequest`.</p>

<?php elseif ($activeLine === 'authentication'): ?>
    <h1>Autentikasi</h1>
    <p>Sistem otentikasi konvensional dapat dengan mudah dikoordinir berbekal dukungan *Facade* `Auth` untuk keperluan fungsional memfasilitasi status Login pengguna.</p>

    <h2>Registrasi & Hashing Objek</h2>
    <p>Dalam mendaftarkan akun baru, konversi Password standar diubah menggunakan <code>Hash::make()</code> berbasis ekstensi inti PHP (*Lihat sesi dokumentasi Hashing*):
    </p>
    <pre><code>use LunoxHoshizaki\Security\Hash;

    public function register(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email|unique:users,email',
            'password' => 'required'
        ]);

        $user = new User();
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect('/login');
    }</code></pre>

    <h2>Fase Logaritma Otentikasi</h2>
    <p>Verifikasi kata sandi secara implisit terkelola tanpa *query* kompleks melalui <code>Auth::attempt</code>, yang terintegrasi secara teknis terhadap format tabel <code>User</code> bawaan:</p>
    <pre><code>use LunoxHoshizaki\Auth\Auth;

    if (Auth::attempt(['email' => $email, 'password' => $password])) {
        return redirect('/dashboard');
    } else {
        return redirect()->back()->withErrors(['message' => 'Email atau password salah.']);
    }</code></pre>

    <h2>Pemanggilan Profil Login Singgah</h2>
    <p>Entitas <code>Auth</code> membawakan akses membedah identitas pengguna di manapun *scoping* aplikasi berjalan:
    </p>
    <pre><code>// Ekstrak referensi instans User yang login di saat yang sama
    $user = Auth::user();
    echo $user->email;

    // Spesifik pencarian Integer penomoran ID
    $id = Auth::id();

    // Operasional pengecekan sederhana *boolean-check*
    if (Auth::check()) {
        // Tervalidasi
    }</code></pre>

    <h2>Membatasi Penetrasi Halaman Melalui Middleware</h2>
    <p>Kombinasi rute privat (Contoh dasbor internal) dicegah akses tidak login-nya melalui injeksi *Class* <code>AuthMiddleware</code>:</p>
    <pre><code>Router::get('/profile', [ProfileController::class, 'show'])
    ->middleware(LunoxHoshizaki\Security\AuthMiddleware::class);</code></pre>

<?php elseif ($activeLine === 'helpers'): ?>
    <h1>Global Helpers</h1>
    <p>Berbagai fungsi bantuan (*helpers*) global mendistribusikan utilitas pengerjaan dasar di semua skope direktori untuk efisiensi akses logika tanpa harus selalu memanggil Namespace objek lengkap.</p>

    <ul>
        <li><code>old('field_name')</code> : Mengembalikan riwayat masukan awal formulir tatkala fase proses input ditolak.</li>
        <li><code>errors('field_name')</code> : Mendapatkan rangkaian nilai pesan kesalahan masukan validasi.</li>
        <li><code>csrf_field()</code> : Men-generate HTML format elemen untuk kebutuhan intervensi serangan keamanan *form request*.</li>
        <li><code>e($string)</code> : Mengonversi objek parameter ke *string HTML escape* yang aman diperlihatkan pada sistem templating.</li>
        <li><code>asset('css/style.css')</code> : Konversi path dokumen absolut menuju sumber File Media pada jalur `public/`.</li>
        <li><code>config('app.name')</code> : Pengambilan objek properti statis spesifik milik arsip pusat konfigurasi.</li>
        <li><code>route('nama_rutemu')</code> : Menampilkan alamat URI berdasar atas penamaan rute.</li>
        <li><code>now()</code> : Fungsi ekstraksi tanggal dan waktu lokal dari kapabelitas PHP (`Y-m-d H:i:s`).</li>
        <li><code>dd($vars), dump($vars)</code> : *Utility tracer* pembantu saat *development* untuk memvisualisasi data koleksional.</li>
        <li><code>collect($array)</code> : Mewakilkan *array format* awam ke kelas Collection yang memiliki ragam bantuan ekstensi pengkondisian iterasi.</li>
    </ul>

<?php elseif ($activeLine === 'redirect'): ?>
    <h1>Redirect (v2.0)</h1>
    <p>Menavigasikan kemudi lalu lintas pengunjung bisa secara halus diperankan lewat asisten <code>Redirect</code> Helper.</p>
    
    <h2>Metode Penggunaan:</h2>
    <pre><code>// Navigasi Standar URL Relatif
    return redirect('/home');

    // Menerjunkan pengunjung kembali menuju rute sebelumnya (Referer Tracker)
    return redirect()->back();

    // Memberikan paket sesi pesan Error Form dengan dukungan *input recovery*
    return redirect()->back()
        ->withErrors(['username' => 'Format masukan telah digunakan'])
        ->withInput();

    // Memberikan tanda *Flash Session* ke UI selanjutnya 
    return redirect('/dashboard')->with('success_alert', 'Proses sinkronisasi data berhasil disahkan!');
    </code></pre>

<?php elseif ($activeLine === 'migrations'): ?>
    <h1>Database Migrations & Seeding</h1>
    <p><i>Migrations</i> memberikan lapisan version control interaktif yang bertugas merancang modifikasi cetakan basis data berkesinambungan bagi semua tim *engineers* tanpa mengeksekusi operasi SQL tertulis manual.</p>

    <h2>Siklus Cetakan Migrasi</h2>
    <p>Deklarasi struktur rintisan tabel dibuat memakai konsole CLI:</p>
    <pre><code>php backfire make:migration create_flights_table</code></pre>
    <p>Hal ini memfasilitasi *draft* dengan tanggal identifikasi yang letaknya di `database/migrations/`. Silakan merekonstruksi skema kolom lewat properti abstrak <code>Schema::create</code>.</p>

    <h2>Contoh Referensi Konstruksi Schema (v2.0)</h2>
    <pre><code>use LunoxHoshizaki\Database\Schema\Schema;
    use LunoxHoshizaki\Database\Schema\Blueprint;

    public function up()
    {
        // Menyediakan Cetakan Tabel
        Schema::create('flights', function (Blueprint $table) {
            $table->id(); // Definisi Primary Key Intejer Bawan (AUTO_INCREMENT)
            $table->string('name', 100); // Format Karakter limitasi (VARCHAR)
            $table->boolean('is_active'); // Format Logika biner (TINYINT)
            $table->timestamps(); // Mendata riwayat integrasi input model default
            $table->softDeletes(); // Konfigurasi *Flagging deletion* via properti `deleted_at` 
        });

        // Contoh Alter/Modifikasi Kolom struktur tabel eksisting
        Schema::table('flights', function (Blueprint $table) {
            $table->renameColumn('is_active', 'status');
        });
    }</code></pre>

    <h2>Instruksi Mengeksekusi Migrasi Sistem</h2>
    <p>Perintahkan *engine* CLI untuk mengubah kerangka struktur logikal file Migration tersebut ke operasi valid Database Engine:</p>
    <pre><code>php backfire migrate</code></pre>

    <h2>Database Seeding (Pengisian Data Sintetis)</h2>
    <p>Kehadiran Seeder meminimalisir intervensi input basis data primitif pada awal pengembangan melalui sistem penyuntikan *Mockup array*.</p>
    <pre><code>// 1. Deklarator Kerangka *Class*
    php backfire make:seeder UserSeeder

    // 2. Tulis konstruksi Dummy record di `app/database/seeders/UserSeeder.php`

    // 3. Modul injeksi keseluruhan baris parameter tersebut
    php backfire db:seed</code></pre>

<?php elseif ($activeLine === 'orm'): ?>
    <h1>Sistem Hubungan ORM</h1>
    <p>Implementasi model entitas *ActiveRecord* mengelaborasi pola relasi tabel database secara dinamis sehingga menyederhanakan penarikan informasi <code>LEFT JOIN</code> terstruktur otomatis untuk skema antar-tabel.</p>

    <h2>Model Relasi Didukung</h2>
    <ul>
        <li><code>hasOne(RelatedModel::class)</code>: Properti korelasi relasi tunggal eksklusif.</li>
        <li><code>hasMany(RelatedModel::class)</code>: Entitas referensi dengan basis distribusi majemuk (Sistem List Data Induk-Anak).</li>
        <li><code>belongsTo(RelatedModel::class)</code>: Mempresentasikan kepemilikan invers tunggal dari definisi relasi lain.</li>
        <li><code>belongsToMany(RelatedModel::class)</code>: Hubungan skala besar majemuk (*Many-to-Many*), lazim mengaplikasikan integrasi *pivot table*.</li>
    </ul>

    <p><b>Referensi Akses Data:</b></p>
    <pre><code>// Identifikasi objek instance induk 
    $user = User::find(1);

    // Kueri relasional mengaitkan properti fungsional array dari relasi terdaftar
    $posts = $user->hasMany(Post::class)->get();</code></pre>

<?php elseif ($activeLine === 'db-transactions'): ?>
    <h1>Database Transactions (v2.0)</h1>
    <p>Sistem transaksi basis data memastikan keselamatan tahapan eksekusi logika mutasi berurutan agar jika ada parameter error tidak ada penyimpanan parsial di struktur database inti.</p>

    <h2>Transaction Closure</h2>
    <pre><code>use LunoxHoshizaki\Database\DB;

    DB::transaction(function() {
        // Proses Operasional Modifikasi Dana (1)
        $dompet = Dompet::find(1);
        $dompet->saldo -= 50000;
        $dompet->save();

        // Kueri Transaksi Pencatatan Sejarah (2)
        $tagihan = new Tagihan();
        $tagihan->nilai = 50000;
        $tagihan->save();
        
        // *Proses komitasi akan divalidasi ketika tidak ada interupsi exception program*
    });
    </code></pre>

<?php elseif ($activeLine === 'storage'): ?>
    <h1>File Storage</h1>
    <p>Intervensi sistem integrasi arsip dokumen dapat terbantu menggunakan fitur pendistribusian lokal sederhana dari kelas `Storage`.</p>
    <pre><code>use LunoxHoshizaki\Storage\Storage;

    // Penyimpanan statis fisik berbasis direktori `public/`
    Storage::put('avatars/1.jpg', $fileDataBinary);

    // Pembuatan string referensi penautan web (URL Asset) untuk keperluan render halaman
    $url = Storage::url('avatars/1.jpg');

    // Fungsi pembersihan arsip secara presisi
    Storage::delete('avatars/1.jpg');</code></pre>

<?php elseif ($activeLine === 'cli'): ?>
    <h1>Pembuatan Perintah Terminal Kustom</h1>
    <p>Rincian operasional rutin layaknya utilitas cron job latar belakang (*scripts maintenance*) dapat disusun pada unit CLI Command tertulis internal aplikasi.</p>
    <pre><code>// Perintah generasi Kelas
    php backfire make:command SendEmails</code></pre>
    <p>File basis perintah akan tercipta di <code>app/Console/Commands/SendEmails.php</code> untuk menampung argumen proses kompensasi data massal. Pemicu utama dijalankan berdasar inisiasi nama spesifik *command signiture*:</p>
    <pre><code>php backfire send:emails</code></pre>

<?php elseif ($activeLine === 'mail'): ?>
    <h1>Mailer System</h1>
    <p>Sistem transmisi surat terpaket *Mailable* difungsikan dalam menyajikan pesan faktur email konfirmasi dengan penataan layout tertata rapi memanfaatkan SMTP native dari library pendukung <b>PHPMailer</b>.</p>

    <h2>Konfigurasi Lingkungan Surat</h2>
    <p>Kredensial infrastruktur pesan elektronik (*SMTP Mail*) berada pada area modifikasi di file `Environment` sentral:
    </p>
    <pre><code>MAIL_MAILER=smtp
    MAIL_HOST=sandbox.smtp.mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=isikan_username
    MAIL_PASSWORD=isikan_password
    MAIL_ENCRYPTION=tls
    MAIL_FROM_ADDRESS="hello@contoh.com"
    MAIL_FROM_NAME="Admin Notifikasi"</code></pre>
    <p>Pengaturan format <code>MAIL_MAILER=smtp</code> menandakan alur kompensasi port berjamin server *Third-Party*, dan meniadakan penggunaan <code>mail()</code> murni di server aplikasi.</p>

    <h2>Protokol Mengirim Email Format Mailable</h2>
    <pre><code>use LunoxHoshizaki\Mail\Mail;

    // Integrasi pemaketan file `class Mailable` kepada entitas alamat yang dirujuk
    Mail::to('user@contoh.com')->send(new WelcomeEmail());</code></pre>

<?php elseif ($activeLine === 'logging'): ?>
    <h1>Logging System (v2.0)</h1>
    <p>Pencatatan rekap sirkulasi log vital program sistem tersimpan otomatis secara aman layaknya penanda rotasi harian dengan susunan `storage/logs/lunox-YYYY-MM-DD.log`.</p>

    <h2>Sintaksis Peringatan Log</h2>
    <pre><code>use LunoxHoshizaki\Log\Log;

    // Log informasi normal dan sukses
    Log::info('Aktivitas Login tercatat di sesi saat ini.', ['user_id' => 5]);

    // Indikasi kesalahan interaksi non-fatal fungsional
    Log::error('API Transaksi gagal membalas tiket.', ['respon_code' => 500]);

    // Respon Kritis Kegagalan Fundamental
    Log::critical('Interupsi Koneksi Database Engine!');
    </code></pre>

<?php elseif ($activeLine === 'cache'): ?>
    <h1>Sistem Cache</h1>
    <p>Penyimpanan entitas berelasi besar dalam file statis memperingan sistem pembacaan kueri terdistribusi *Database server* selama skala rentang penahanan rotasi tertentu yang diatur.</p>
    <p><b>SECURITY NOTE (V2.0):</b> Format skema integrasi *engine* pemanggil file Cache menggunakan standar JSON *encoder* yang terhindar dari pemanfaatan celah injeksi via modul pembawa `unserialize()`. Jalankan <code>php backfire cache:clear</code> pasca revisi versi.</p>
    
    <pre><code>use LunoxHoshizaki\Cache\Cache;

    // Memuat penahanan nilai informasi khusus sampai batasan kalkulasi (waktu detik)
    Cache::put('key_rahasia_data', 'Sistem Konfigurasi Beban', 3600);

    // Identifikasi nilai objek dan penyertaan balasan alternatif (*Fallback parameter*)
    $value = Cache::get('key_rahasia_data', 'default');</code></pre>

<?php elseif ($activeLine === 'events'): ?>
    <h1>Event Observers</h1>
    <p>Isolasi logika bisnis ekstra (misal mengirim format faktur) dengan membagi instansi pemrograman pemancar (<i>Publisher Event Actions</i>) dan penerima intervensinya (*Listener*).</p>
    <pre><code>use LunoxHoshizaki\Events\Event;

    // Menciptakan pancaran pemicu event sistem
    Event::dispatch(new UserRegistered($user));

    // Eksekutor pendaftar fungsi logis apabila interaksi rute spesifik terpancarkan 
    Event::listen(UserRegistered::class, SendWelcomeEmail::class);</code></pre>

<?php elseif ($activeLine === 'security'): ?>
    <h1>Pemeliharaan Server Dasar</h1>
    <p>Struktur model proteksi ISO dan mitigasi standar OWASP dapat diperbantukan pada level arsitektur middleware sistem aplikasi.</p>

    <h2>Rate Limiting</h2>
    <p>Lapisan *throttle* ditanamkan sebagai modul pengawasan jumlah lalu-lintas permintaan pada unit alamat IP tertentu; hal yang lazim digunakan demi proteksi DDoS (*Brute-force limit*).</p>
    <pre><code>use LunoxHoshizaki\Security\ThrottleRequests;

    Router::get('/api/data', [ApiController::class, 'index'])
        ->middleware(ThrottleRequests::class);</code></pre>

    <h2>Mitigasi XSS Injeksi</h2>
    <p>Konversi rendering pengolahan karakter dari parameter eksternal dimuat melewati mekanisme mitigasi String dengan fungsi <code>e()</code> untuk meminimalisasi celah manipulasi skrip (*Cross-Site Scripting*).</p>
    <pre><code>&lt;!-- Implementasi pelolosan yang sah format HTML --&gt;
    &lt;?= e($user->bio) ?&gt;</code></pre>

    <h2>Pengaturan Identitas Skema CORS</h2>
    <p>Rilis rute pada sistem luar beda lingkup Domain (Aplikasi UI *SPA* React dsb) dikelompokan di dalam fungsi pengawasan `CorsMiddleware`.</p>
    <pre><code>use LunoxHoshizaki\Security\CorsMiddleware;

    Router::prefix('/api')->middleware([CorsMiddleware::class])->group(function () {
        Router::get('/users', [ApiController::class, 'users']);
    });</code></pre>

    <h2>Header Properti HTTP Lanjutan (v2.0)</h2>
    <p>Protektif skema MIME Header (*CSP/ClickJacking*) secara mutlak diformat melalui rincian arsitektural *Class SecureHeadersMiddleware* yang dapat meredam manipulasi modifikasi akses browser tidak terverifikasi.
    </p>
    <pre><code>use LunoxHoshizaki\Security\SecureHeadersMiddleware;

    Router::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(SecureHeadersMiddleware::class);</code></pre>

<?php elseif ($activeLine === 'csrf'): ?>
    <h1>Proteksi CSRF</h1>
    <p>Pengamanan formulir mencegah serangan kerentanan *Cross-Site Request Forgery (CSRF)* yang memungkinkan parameter rute di luar batas otorisasi pengguna ditransmisikan dan dimanipulasi.</p>

    <h2>Mekanisme Alur Verifikasi Form</h2>
    <p>Identifikasi log sesi secara tertutup mencatat kepemilikan string "Token CSRF". Validasi operasional *HTTP Method* modifikasi Data seperti baris PUT, POST dan DELETE diwajibkan menyertakan pengajuan Token tervalidasi yang relevan agar tak tertolak skema Respon <code>419 Expired</code>.</p>

    <h2>Menyiapkan Pengajuan Parameter HTML</h2>
    <p>Elemen baris rahasia di dalam struktur cetakan formular didukung *Function Render* instansi token tersebut via sisipan <code>csrf_field()</code>:</p>
    <pre><code>&lt;form action="/posts/simpan" method="POST"&gt;
        &lt;?= csrf_field() ?&gt;

        &lt;label&gt;Judul Tulisan:&lt;/label&gt;
        &lt;input type="text" name="judul"&gt;

        &lt;button type="submit"&gt;Posting&lt;/button&gt;
    &lt;/form&gt;</code></pre>

    <h2>Konfigurasi Interaksi AJAX</h2>
    <p>Transmisi formulir asinkron secara *Javascript API* mendukung parameter identifikasi referensi penempatan *meta-tags*.</p>
    <pre><code>&lt;meta name="csrf-token" content="&lt;?= $_SESSION['csrf_token'] ?? '' ?&gt;"&gt;</code></pre>
    <p>Header pengajuan tersebut dimanfaatkan oleh integrasi arsitektur request kustom eksternal:</p>
    <pre><code>// Modifikasi parameter *Header Request* di klien web
    let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;</code></pre>

<?php elseif ($activeLine === 'hashing'): ?>
    <h1>Hashing (v2.0)</h1>
    <p>Kelas Hash secara cerdas mengadaptasi algoritma kriptografi tercanggih berstandar (seperti Argon2Id atau BCrypt) dalam pengolahan hash yang aman.</p>

    <h2>Format Otentikasi Terenkripsi</h2>
    <pre><code>use LunoxHoshizaki\Security\Hash;

    // Sistem secara otomatis mendeteksi penyandian terbaik kompilasi server PHP
    $password_aman = Hash::make('P4ssw0rdKuat!');

    // Konfirmasi keamanan integrasi algoritma sandi
    if (Hash::check('P4ssw0rdKuat!', $password_aman)) {
        echo "Valid Cuy!";
    }
    
    // Mengevaluasi validitas format dan kecocokan algoritma sistem sekarang dengan format lawas
    if (Hash::needsRehash($password_aman)) {
        // Melakukan generate struktur kriptografi *Upgraded standard*
    }
    </code></pre>

<?php elseif ($activeLine === 'str'): ?>
    <h1>String Parsing <code>Str</code> (v2.0)</h1>
    <p>Berbagai referensi standar manajemen kata logis diimplementasi di modul fasilitator utilitas fungsional *String Extractor* statis pada Class <code>Str</code>.</p>

    <pre><code>use LunoxHoshizaki\Support\Str;

    // Mutator properti baris kata pemformat URL
    echo Str::slug('Pemformat Parameter URL'); // "pemformat-parameter-url"

    // Standar pengacakan indeks acuan Identitas v4 Universial (UUID)
    echo Str::uuid(); 

    // Intervensi konversi tata teks program
    echo Str::snake('UserControllerName'); // "user_controller_name"

    // Limitator referensi kuantitas karakter awam pada suatu referensi *preview*
    echo Str::limit('Pembatasan deskripsi tampilan antarmuka web sistem', 15); // "Pembatasan de..."
    </code></pre>

<?php elseif ($activeLine === 'collections'): ?>
    <h1>Object Collections (v2.0)</h1>
    <p>Utilitas Collection yang merangkul struktur data *Array Multidimensi* mendukung sintaks interaktif dengan antarmuka yang lebih tertata jika disejajarkan oleh skema nativ php.</p>

    <h2>Fungsi Pemrosesan Integratif Set Matrix</h2>
    <pre><code>// Konstruksi basis tabel matriks entri array terkelola
    $data_kumpulan = [
        ['id' => 1, 'nama' => 'Agus', 'skor' => 90],
        ['id' => 2, 'nama' => 'Bambang', 'skor' => 50],
        ['id' => 3, 'nama' => 'Budi', 'skor' => 70],
    ];

    // Interaksi abstraksi tipe formasi pengolahan himpunan data
    $collection = collect($data_kumpulan);

    // Kumpulan sintaks konversi yang di-chaining
    $lulusan = $collection
        ->where('skor', '>=', 70) 
        ->sortBy('skor', true) 
        ->pluck('nama') 
        ->toArray();

    dump($lulusan); // Array: ['Agus', 'Budi']
    </code></pre>
    
    <p>Manajemen operasional himpunan terprogram mendukung fungsi standar industri lainnya meliputi: <code>map, filter, reduce, chunk, merge, keyBy, dll.</code></p>

<?php elseif ($activeLine === 'request-response'): ?>
    <h1>Representasi Request & Response</h1>
    <p>Konfigurasi parameter web sistem diterjemah sebagai siklus dua entitas objek yakni parameter pengguna yang diminta (**Request**) dan bentuk respon (*Rendering/JSON*) antarmuka (**Response**).</p>

    <h2>Intervensi Payload Request Data</h2>
    <p>Instansiasi abstrak pada objek <code>LunoxHoshizaki\Http\Request</code> meredam kompleksitas pembacaan skrip URI secara konsisten dari skema eksekusi operasi HTTP Form Data maupun properti parameter navigasi link GET.</p>
        
    <h3>Bentuk Data Tipikal (v2.0 Feature)</h3>
    <p>Fungsi tipikal pendefinisian permintaan difilter lebih awal (*Typecasting*) untuk keamanan konsistensi komparasi format basis data sistem yang ketat.</p>    
    <pre><code>public function store(Request $request)
    {
        // Penerimaan konversi parameter teks dan mitigasi standar
        $name = $request->string('name');

        // Parameter numerikal murni dan pengaturan kompensitas `Default Constraint` (18)
        $age = $request->integer('age', 18);

        // Penerimaan tipe pemastian logika format spesifik bernilai Booleoan (`true`/`false`)
        $is_agree = $request->boolean('terms_agreed');

        // Referensi pembacaan alur arsip dokumen File Media fisik 
        if ($request->hasFile('avatar')) {
             // Operasional pergerakan Media
        }
    }</code></pre>

    <h2>Inisiasi Format Sistem Respon Data Tervalidasi</h2>
    <p>Rincian properti objek Respon memungkinkan pengalihan spesifik terhadap Header MIME *Status code* di mana format integrasi *Content-Type* spesifik dimanipulasi layaknya arsitektural API.
    </p>
    <pre><code>use LunoxHoshizaki\Http\Response;

    public function getJsonInfo()
    {
        $response = new Response();
        $response->setContent(json_encode([
            'status' => 'success', 
            'version' => '2.0.0'
        ]));

        // Asosiasi tipe pengembalian dokumen yang diarahkan khusus *JSON-Only* (201 Eksekusi)
        $response->setHeader('Content-Type', 'application/json');
        $response->setStatusCode(201);

        return $response;
    }</code></pre>

<?php elseif ($activeLine === 'errors'): ?>
    <h1>Sistem Error Handler</h1>
    <p>Resepsi konfigurasi rincian eksekutor kueri *Trace Exception* pada sistem *Production Environment Server* dialihkan layaknya notifikasi yang aman serta seragam dari visual pengguna akhir.</p>

    <h2>Visual Konfigurasi Tampilan Otomatis</h2>
    <p>Cakupan kesalahan referensial eksekusi parameter fatal diarahkan secara tunggal melingkungi *template view fallback* eksternal yang eksis pada <code>resources/views/errors/error.php</code>.</p>
    <ul>
        <li><b>404 Tautan Tak Tersedia</b>: Tangkapan atas kegagalan eksistensi sistem dari *controller finder*.</li>
        <li><b>500 Kegagalan Logika Mesin Server</b>: Pemroses kode bermasalah.</li>
        <li><b>419 Kedaluwarsa Sesi</b>: Referensi CSRF *Token Timeout* yang ditahan.</li>
        <li><b>429 Resepsi *Throttle* Terlampaui</b>: Notifikasi pembatasan integrasi masukan jaringan akses (*Rate Limit*).</li>
    </ul>

<?php elseif ($activeLine === 'env'): ?>
    <h1>Environment Base (.env)</h1>
    <p>Abstraksi manajemen rahasia pengontrol pusat kredensial dipisahkan secara hierarki parameter dari entri program operasional koding (*source controller*).</p>

    <h2>Deklarasi Konstanta Server Global</h2>
    <p>Framework membedah konfigurasi dari komponen pembaca eksternal berformat isolasi root server <code>.env</code>.</p>
    <pre><code>APP_NAME="Aplikasi Produksi"
    APP_ENV=local
    APP_DEBUG=true

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=db_core
    DB_USERNAME=root
    DB_PASSWORD=secret</code></pre>
    <p>Parameter sensitivitas dokumen dikecualikan dari komit repo utama via penyertaan di ekstensi <code>.gitignore</code> standar *security compliance*.</p>

    <h2>Sistem Penyaringan Variabel Properti Bawaan (v2.0)</h2>
    <p>Prosedur utilitas sistem meniadakan kemungkinan eksistensi variabel kosong jika dimuat skriptual saat nilai terdegradasi / absen tanpa peredam eror *Fallback parameter* bawaan di dalam *array* sistem.</p>
    
    <pre><code>// Modifikasi penangkapan parameter sistem spesifik dari konfigurasi Environtment (*Helper*)
    $secret = env('DB_PASSWORD', 'NilaiFallbackPenting');

    // Menarik hierarki manajemen Array asosiatif bertingkat dari folder Konfigurasi spesifik `.dot-syntax`
    $namaWeb = config('app.name', 'Framework Ajib');
    </code></pre>

<?php elseif ($activeLine === 'artisan'): ?>
    <h1>Interaksi Terminal Manajemen Konsol</h1>
    <p>Sistem intervensi manajemen komando perintah *script executable* berjenis <code>backfire</code> berdiam sebagai basis aplikasi portabel pelaksana administrasi utiliatas pengembang (*Developer Toolbar CLI*).</p>

    <h2>Sintaksis Manajemen Skrip Terminal</h2>
    <p>Penginisialisasian utama intervensi operasi berjalan dieksekusi dengan *PHP Interpreter Engine* sebagai rintisan argumen di panel komando.</p>
    <pre><code>php backfire</code></pre>

    <p>Berikut adalah beberapa komponen instruksi *Scaffolding builder* (*File Generator Template*) yang disertakan untuk pemrosesan lebih ringkas:</p>
    <ul>
        <li><code>... make:controller DataController</code> &rarr; Membuat fondasi model klasifikasi controller rute program.</li>
        <li><code>... make:request StoreDataRequest</code> &rarr; Pembangkit formulasi Custom Model Validator form input (v2.0 update).</li>
        <li><code>... make:model LogSistem</code> &rarr; Menciptakan relasional sistem pembaca kelas Tabel (*Object Relational Mapping*).</li>
        <li><code>... make:middleware UserScope</code> &rarr; Menghadirkan pengerjaan modul lapis filter pencegah intervensi akses log.
        </li>
        <li><code>... make:migration add_history_table</code> &rarr; Membentuk versi pemetaan kerangka tabel struktural rancang bangun MySQL Engine.
        </li>
        <li><code>... migrate</code> &rarr; Menjalankan proses implementasi cetakan skema mutakhir pada pangkalan server database sentral.</li>
        <li><code>... db:seed</code> &rarr; Memicu populasi sampel dummy rekam jejak masal terhadap parameter data lokal simulasi pengembangan.</li>
        <li><code>... key:generate</code> &rarr; Mensinergi ulang pembuatan angka kunci basis aplikasi demi kriptografi internal teraman.</li>
        <li><code>... cache:clear</code> &rarr; Merekonstruksi dan melegakan retensi ukuran berkas *data caching* stagnasi lama.</li>
        <li><code>... serve</code> &rarr; Menjadi lokomotif pembantu simulasi Web Development Engine secara localhost.</li>
    </ul>

<?php endif; ?>

<?php View::endsection(); ?>
```

## components/navbar.php

```php
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

```

## layouts/app.php

```php
<?php use LunoxHoshizaki\View\View; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Lunox Hoshizaki">
    <title><?php echo htmlspecialchars($title ?? $_ENV['APP_NAME'] ?? 'Lunox Backfire'); ?></title>
    <!-- Google Fonts: Google Sans Flex and Material Symbols -->
    <link
        href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@8..144,100..1000&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap"
        rel="stylesheet">
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Google Sans Flex', system-ui, -apple-system, sans-serif;
            color: #333;
        }

        .main-content {
            min-height: calc(100vh - 140px);
            padding: 40px 0;
        }

        .gradient-text {
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .material-symbols-outlined {
            vertical-align: middle;
            line-height: 1;
        }
    </style>
</head>

<body>

    <?php View::component('basic.components.navbar'); ?>

    <main class="main-content container">
        <?php View::yield('content'); ?>
    </main>

    <footer class="bg-dark text-light py-4 mt-auto">
        <div class="container text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?>
                <strong><?php echo $_ENV['APP_NAME'] ?? 'Lunox Backfire'; ?></strong>
                v<?php echo $_ENV['APP_VERSION'] ?? '1.1.0'; ?>. Designed by Lunox Hoshizaki.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
```

## layouts/docs.php

```php
<?php use LunoxHoshizaki\View\View; ?>
<?php View:: extends('basic.layouts.app'); ?>

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

    /* v2.0 new badge */
    .nav-badge-new {
        display: inline-block;
        font-size: 0.6rem;
        font-weight: 700;
        background: #0d6efd;
        color: #fff;
        padding: 0.1rem 0.35rem;
        border-radius: 0.25rem;
        vertical-align: middle;
        margin-left: 4px;
        letter-spacing: 0.03em;
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

    /* v2.0 info/warning callouts */
    .docs-callout {
        border-left: 4px solid;
        padding: 0.85rem 1.25rem;
        border-radius: 0 0.5rem 0.5rem 0;
        margin: 1.5rem 0;
        font-size: 0.95rem;
    }
    .docs-callout-info  { border-color: #0d6efd; background: rgba(13,110,253,.07); }
    .docs-callout-warn  { border-color: #ffc107; background: rgba(255,193,7,.08); }
    .docs-callout-danger{ border-color: #dc3545; background: rgba(220,53,69,.07); }
    .docs-callout strong { display: block; margin-bottom: .25rem; }
</style>

<div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 d-none d-md-block docs-sidebar border-end pe-3">
        <nav class="nav flex-column mb-5">
            <h4 class="docs-nav-title">Prologue</h4>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'installation' ? 'active' : ''; ?>"
                href="/docs/installation">Installation</a>

            <h4 class="docs-nav-title">The Basics</h4>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'routing' ? 'active' : ''; ?>"
                href="/docs/routing">Routing</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'middleware' ? 'active' : ''; ?>"
                href="/docs/middleware">Middleware</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'controllers' ? 'active' : ''; ?>"
                href="/docs/controllers">Controllers</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'views' ? 'active' : ''; ?>"
                href="/docs/views">Views</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'request-response' ? 'active' : ''; ?>"
                href="/docs/request-response">Request &amp; Response</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'validation' ? 'active' : ''; ?>"
                href="/docs/validation">Validation</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'authentication' ? 'active' : ''; ?>"
                href="/docs/authentication">Authentication</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'helpers' ? 'active' : ''; ?>"
                href="/docs/helpers">Helpers &amp; Session</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'redirect' ? 'active' : ''; ?>"
                href="/docs/redirect">Redirect <span class="nav-badge-new">v2</span></a>

            <h4 class="docs-nav-title">Database</h4>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'database' ? 'active' : ''; ?>"
                href="/docs/database">Models &amp; Active Record</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'migrations' ? 'active' : ''; ?>"
                href="/docs/migrations">Database Migrations</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'orm' ? 'active' : ''; ?>" href="/docs/orm">ORM
                Relationships</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'db-transactions' ? 'active' : ''; ?>"
                href="/docs/db-transactions">DB Transactions <span class="nav-badge-new">v2</span></a>

            <h4 class="docs-nav-title">Services</h4>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'cache' ? 'active' : ''; ?>"
                href="/docs/cache">Cache System</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'events' ? 'active' : ''; ?>"
                href="/docs/events">Events &amp; Listeners</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'storage' ? 'active' : ''; ?>"
                href="/docs/storage">File Storage</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'mail' ? 'active' : ''; ?>"
                href="/docs/mail">Mailer System</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'logging' ? 'active' : ''; ?>"
                href="/docs/logging">Logging <span class="nav-badge-new">v2</span></a>

            <h4 class="docs-nav-title">Security</h4>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'security' ? 'active' : ''; ?>"
                href="/docs/security">Feature Security &amp; DDOS</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'csrf' ? 'active' : ''; ?>"
                href="/docs/csrf">CSRF Protection</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'hashing' ? 'active' : ''; ?>"
                href="/docs/hashing">Hashing <span class="nav-badge-new">v2</span></a>

            <h4 class="docs-nav-title">Utilities</h4>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'str' ? 'active' : ''; ?>"
                href="/docs/str">String Helper <span class="nav-badge-new">v2</span></a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'collections' ? 'active' : ''; ?>"
                href="/docs/collections">Collections <span class="nav-badge-new">v2</span></a>

            <h4 class="docs-nav-title">Digging Deeper</h4>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'errors' ? 'active' : ''; ?>"
                href="/docs/errors">Error Handling</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'env' ? 'active' : ''; ?>"
                href="/docs/env">Environment Configuration</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'artisan' ? 'active' : ''; ?>"
                href="/docs/artisan">Backfire Console</a>
            <a class="docs-nav-link <?php echo ($activeLine ?? '') === 'cli' ? 'active' : ''; ?>"
                href="/docs/cli">Custom CLI Commands</a>
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
```


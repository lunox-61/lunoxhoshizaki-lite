<?php use LunoxHoshizaki\View\View; ?>
<?php View:: extends('layouts.docs'); ?>

<?php View::section('docs-content'); ?>

<?php if ($activeLine === 'installation'): ?>
    <h1>Installation & Architecture</h1>
    <p>Selamat datang di Lunox Backfire. Sebuah framework PHP berkinerja tinggi hasil eksperimen khusus yang memiliki
        kemudahan layaknya Laravel, tetapi jauh lebih ringan dan cepat. Panduan ini akan membantumu memahami kerangka kerja
        dasar agar lebih mudah membangun aplikasi modern yang aman dan andal.</p>

    <h2>Cara Instalasi Framework</h2>
    <p>Berikut langkah-langkah instalasi dan konfigurasi proyek skeleton <b>lunoxhoshizaki-lite</b> kamu dari GitHub:</p>

    <p><b>1. Clone Repository</b></p>
    <p>Bila kamu ingin nama folder proyekmu berbeda dengan nama *repository* asli (misal: ingin menamainya
        <code>ecommerce</code> atau <code>my-app</code>), tambahkan nama folder tersebut di akhir baris perintah
        <code>git clone</code>:</p>
    <pre><code># Format: git clone [URL_REPO] [NAMA_FOLDER_KAMU]
    git clone https://github.com/lunox-61/lunoxhoshizaki-lite.git ecommerce
    cd ecommerce</code></pre>

    <p><b>2. Install Dependencies Pihak Ketiga</b></p>
    <p>Jalankan Composer untuk mengunduh pustaka wajib (termasuk vlucas/phpdotenv):</p>
    <pre><code>composer install</code></pre>

    <p><b>3. Dump Autoload</b></p>
    <p>Regenerasi peta autoloader agar semua class framework dapat dimuat dengan baik:</p>
    <pre><code>composer dump-autoload -o</code></pre>

    <p><b>4. Setup Environment</b></p>
    <p>Buat file konfigurasi `.env` dengan menyalin bawaannya:</p>
    <pre><code>cp .env.example .env</code></pre>

    <h2>Requirements</h2>
    <p>Framework ini dirancang untuk PHP versi terbaru, pastikan kamu memiliki:</p>
    <ul>
        <li>PHP >= 8.0 (disarankan 8.4+)</li>
        <li>PDO & OpenSSL PHP Extension</li>
        <li>Sistem database MySQL / SQLite</li>
    </ul>

    <h2>Struktur Direktori</h2>
    <p>Setelah menginstal / mengkloning repositori, kamu akan melihat direktori penting berikut:</p>
    <ul>
        <li><b><code>app/</code></b> : Berisi logika aplikasi utamamu. Models, Controllers, Listeners, dan Events disimpan
            di sini.</li>
        <li><b><code>database/</code></b> : Folder untuk file Migrations (pembuat otomatis tabel database) dan Seeders
            (suntikan data awal).</li>
        <li><b><code>public/</code></b> : Akses masuk (`index.php`) web. Simpan file asetmu (CSS, JS, Images) di sini.</li>
        <li><b><code>resources/views/</code></b> : Memuat seluruh struktur Front-End UI kamu (HTML / Template Web).</li>
        <li><b><code>routes/</code></b> : Definisikan setiap Endpoint/URL websitemu di sini (<code>web.php</code> dan
            <code>api.php</code>).</li>
        <li><b><code>src/</code></b> : <i>Ini adalah Inti Mesin Framework.</i> Jangan diubah kecuali kamu mengerti apa yang
            kamu lakukan!</li>
    </ul>

    <h2>Server Configuration (Development)</h2>
    <p>Kamu tidak perlu mengatur Apache atau Nginx saat dalam masa pengembangan. Backfire dilengkapi server internal super
        cepat. Cukup ketik perintah CLI (Command Line Interface) berikut di terminalmu:</p>
    <pre><code>php backfire serve</code></pre>
    <p>Website kamu kini online secara live di <code>http://localhost:8000</code>.</p>

<?php elseif ($activeLine === 'routing'): ?>
    <h1>Routing (Alur URL)</h1>
    <p>Routing adalah pintu masuk utama aplikasimu. Di sinilah kamu mendefinisikan URL apa saja yang bisa diakses oleh
        pengunjung dan ke mana mereka akan diarahkan.</p>

    <h2>Basic Web Routing</h2>
    <p>Semua rute web didefinisikan di dalam file <code>routes/web.php</code>. Rute paling sederhana mengembalikan string
        teks atau langsung memanggil <i>View</i>:</p>
    <pre><code>use LunoxHoshizaki\Routing\Router;

    Router::get('/halo', function () {
        return 'Halo Dunia!';
    });</code></pre>

    <h2>Route Parameters (URL Dinamis)</h2>
    <p>Terkadang kamu perlu menangkap nilai dari URL, misalnya ID pengguna. Gunakan tanda kurung kurawal <code>{ }</code>:
    </p>
    <pre><code>Router::get('/user/{id}', function ($request, $id) {
        return 'Profil Pengguna ID: ' . $id;
    });</code></pre>

    <h2>Metode HTTP Lainnya (POST, PUT, DELETE)</h2>
    <p>Untuk menangkap kiriman formulir (form submit) atau request API, kamu bisa menggunakan pendefinisian rute berikut:
    </p>
    <pre><code>Router::post('/user/simpan', [UserController::class, 'store']);
    Router::put('/user/{id}/update', [UserController::class, 'update']);
    Router::delete('/user/{id}/hapus', [UserController::class, 'destroy']);</code></pre>

    <h2>Grup Rute & Middleware</h2>
    <p>Bila kamu punya banyak URL yang memerlukan aturan yang sama (misalnya: hanya boleh diakses bila sudah login), kamu
        bisa mengelompokkannya:</p>
    <pre><code>Router::group(['prefix' => '/admin', 'middleware' => [AuthMiddleware::class]], function () {
        Router::get('/dashboard', [AdminController::class, 'index']);
        Router::get('/setting', [AdminController::class, 'setting']);
    });</code></pre>

<?php elseif ($activeLine === 'middleware'): ?>
    <h1>Middleware (Penengah)</h1>
    <p><i>Middleware</i> menyediakan mekanisme yang nyaman untuk menengahi (memfilter) permintaan HTTP yang masuk atau
        keluar dari aplikasi kamu. Sebagai contoh, Backfire menyertakan middleware yang memverifikasi apakah pengguna
        aplikasi sudah terautentikasi (login) atau belum.</p>

    <h2>Membuat Middleware</h2>
    <p>Untuk membuat middleware baru, kamu bisa menggunakan perintah CLI `make:middleware`:</p>
    <pre><code>php backfire make:middleware CheckAge</code></pre>
    <p>Perintah ini akan menempatkan kelas `CheckAge` yang baru di dalam direktori `app/Http/Middleware` (atau
        `app/Middleware` tergantung strukturmu).</p>

    <p>Di dalam middleware, kamu perlu menulis logika di fungsi `handle`. Contoh menolak pengguna jika usianya kurang dari
        18 tahun:</p>
    <pre><code>namespace App\Middleware;

    use LunoxHoshizaki\Http\Request;

    class CheckAge
    {
        public function handle(Request $request, \Closure $next)
        {
            if ($request->input('age') < 18) {
                return redirect('/home');
            }

            // Lanjutkan permintaan
            return $next($request);
        }
    }</code></pre>

    <h2>Mendaftarkan & Menggunakan Middleware</h2>
    <p>Setelah dibuat, kamu bisa langsung menugaskannya ke rute (*route*) secara spesifik di `routes/web.php` atau
        `routes/api.php`.</p>
    <pre><code>use App\Middleware\CheckAge;

    // Menerapkan middleware pada satu rute spesifik
    Router::get('/khusus-dewasa', function () {
        return 'Selamat datang di area khusus!';
    })->middleware(CheckAge::class);

    // Menerapkan middleware ke sekelompok rute
    Router::group(['prefix' => '/admin', 'middleware' => [CheckAge::class]], function () {
        Router::get('/dashboard', [AdminController::class, 'index']);
    });</code></pre>

<?php elseif ($activeLine === 'controllers'): ?>
    <h1>Controllers</h1>
    <p>Menulis semua logika di dalam file rute (<code>web.php</code>) akan membuat kodenya berantakan. <b>Controllers</b>
        adalah kelas khusus untuk mengelompokkan logika-logika yang berkaitan di satu tempat.</p>

    <h2>Membuat Controller Baru</h2>
    <p>Gunakan Backfire CLI agar kamu tidak perlu mengetik kerangka kodenya secara manual:</p>
    <pre><code>php backfire make:controller UserController</code></pre>
    <p>File baru akan muncul di <code>app/Controllers/UserController.php</code>.</p>

    <h2>Contoh Struktur Controller</h2>
    <p>Sebuah Controller biasanya merespons terhadap suatu <i>Request</i> lalu mengembalikan HTML (<i>View</i>) atau JSON:
    </p>
    <pre><code>namespace App\Controllers;

    use LunoxHoshizaki\Http\Request;
    use LunoxHoshizaki\View\View;
    use LunoxHoshizaki\Http\Response;

    class UserController
    {
        // Mengembalikan halaman HTML
        public function show(Request $request, $id)
        {
            return View::make('user.profile', ['id' => $id]);
        }

        // Mengembalikan data JSON (Cocok untuk API)
        public function getJson(Request $request)
        {
            $response = new Response();
            $response->setContent(json_encode(['status' => 'success', 'data' => 'Data Rahasia']));
            $response->setHeader('Content-Type', 'application/json');
            return $response;
        }
    }</code></pre>

    <h2>Menyambungkan Controller ke Route</h2>
    <p>Panggil nama Class Controller dan nama funciton-nya sebagai <i>Array</i> di file rute:</p>
    <pre><code>Router::get('/user/{id}', [UserController::class, 'show']);</code></pre>

<?php elseif ($activeLine === 'views'): ?>
    <h1>Views (Halaman Antarmuka)</h1>
    <p><i>Views</i> bertugas memisahkan logika aplikasi dengan tampilan HTML yang dilihat oleh pengguna. Semua file tampilan
        disimpan di dalam direktori <code>resources/views</code>.</p>

    <h2>Membuat & Menampilkan View</h2>
    <p>Gunakan method statis <code>View::make</code> di Controller untuk memanggil file (ekstensi <code>.php</code> tidak
        perlu ditulis):</p>
    <pre><code>// Akan memanggil file resources/views/greeting.php
    return View::make('greeting', ['nama' => 'Budi']);</code></pre>
    <p>Di dalam file <code>greeting.php</code>, kamu bisa langsung mencetak datanya (pastikan menggunakan helper
        <code>e()</code> untuk keamanan):</p>
    <pre><code>&lt;h1&gt;Halo, &lt;?= e($nama) ?&gt;!&lt;/h1&gt;</code></pre>

    <h2>Layouts & Template Inheritance</h2>
    <p>Agar kamu tidak menulis ulang kode <code>&lt;head&gt;</code> dan <code>&lt;footer&gt;</code> berkali-kali, gunakan
        fitur ekstensi layout:</p>

    <p><b>1. Buat Master Layout (<code>resources/views/layouts/app.php</code>):</b></p>
    <pre><code>&lt;html&gt;
    &lt;head&gt;&lt;title&gt;Aplikasi Keren&lt;/title&gt;&lt;/head&gt;
    &lt;body&gt;
        &lt;?= $this-&gt;yieldContent('content') ?&gt;
    &lt;/body&gt;
    &lt;/html&gt;</code></pre>

    <p><b>2. Gunakan Layout Tersebut di Halaman Lain:</b></p>
    <pre><code>&lt;?php View::extends('layouts.app'); ?&gt;

    &lt;?php View::section('content'); ?&gt;
        &lt;h1&gt;Hai, ini konten spesifik halaman!&lt;/h1&gt;
    &lt;?php View::endsection(); ?&gt;</code></pre>

<?php elseif ($activeLine === 'database'): ?>
    <h1>Database & Models</h1>
    <p>Lunox Backfire hadir dengan implementasi <i>ActiveRecord</i> super sederhana yang otomatis mengelola operasi database
        tanpa perintah SQL panjang.</p>

    <h2>Konfigurasi Koneksi</h2>
    <p>Sebelum menggunakan database, pastikan file konfigurasi <code>.env</code> milikmu sudah diisi parameter yang sesuai
        dengan server MySQL lokalmu:</p>
    <pre><code>DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=lunox_lite
    DB_USERNAME=root
    DB_PASSWORD=</code></pre>

    <h2>Mendefinisikan Model</h2>
    <p>Setiap tabel database idealnya memiliki satu <b>Model</b>. Gunakan Backfire CLI:</p>
    <pre><code>php backfire make:model Product</code></pre>
    <p>Secara default, Backfire akan mencari tabel yang berwujud kata jamak bahasa Inggris dari Model (misal: kelas
        `Product` -> mencari tabel <code>products</code>).</p>

    <h2>Operasi CRUD (Create, Read, Update, Delete)</h2>
    <p>Menyimpan baris baru ke dalam database sangatlah mudah:</p>
    <pre><code>// CREATE: Menyimpan data baru
    $produk = new Product();
    $produk->name = 'Buku Panduan';
    $produk->price = 150000;
    $produk->save();

    // READ: Mengambil satu atau beberapa data
    $satuProduk = Product::find(1); // Mencari ID 1
    $semuaProduk = Product::where('price', '>', 50000)->get();

    // UPDATE: Memperbarui data
    $produkLama = Product::find(1);
    $produkLama->price = 100000;
    $produkLama->save();

    // DELETE: Menghapus data
    $produkHapus = Product::find(2);
    $produkHapus->delete();</code></pre>

    <h2>Advanced Query Builder & Pagination</h2>
    <p>Bila kamu memerlukan kueri kompleks beserta navigasi halaman otomatis bergaya Bootstrap 5:</p>
    <pre><code>// Pengambilan data menggunakan fitur urut dan limit
    $terlaris = Product::query()
        ->where('stock', '>', 0)
        ->orderBy('sold_count DESC')
        ->limit(10)
        ->get();

    // Otomatis pecah hasil pencarian 15 item per halaman
    $semuaProduk = Product::paginate(15);
    </code></pre>

    <p>Lalu, render tombol Paginasi di <i>View</i> halamanmu:</p>
    <pre><code>&lt;!-- Rander HTML Paginasi bawaan Backfire --&gt;
    &lt;?= $semuaProduk-&gt;links() ?&gt;</code></pre>

<?php elseif ($activeLine === 'validation'): ?>
    <h1>Validation & Forms</h1>
    <p>Backfire memiliki mesin validasi bawaan pada objek <code>Request</code> yang akan sangat berguna mengamankan input
        formulir (form) mu.</p>

    <h2>Memvalidasi Request Masuk</h2>
    <p>Cukup atur *rules* yang ingin diaplikasikan pada input yang dikirimkan klien:</p>
    <pre><code>public function store(Request $request)
    {
        // Jika validasi gagal, proses terhenti dan kembali ke halaman sebelumnya
        $validated = $request->validate([
            'title' => 'required|max:255',
            'body'  => 'required',
            'email' => 'required|email'
        ]);

        // Lanjutkan menyimpan ke database...
        $post = new Post();
        $post->title = $validated['title'];
        $post->save();

        return redirect('/posts');
    }</code></pre>

    <h2>Menampilkan Error Validasi di HTML Formulir</h2>
    <p>Data salah atau *error* akan di-*flash* secara otomatis ke Sesi. Gunakan Helper global <code>errors()</code> dan
        <code>old()</code> untuk kemudahan UI:</p>
    <pre><code>&lt;form action="/simpan" method="POST"&gt;
        &lt;!-- Selalu masukkan CSRF Token untuk mencegah serangan forgery! --&gt;
        &lt;?= csrf_field() ?&gt;

        &lt;label&gt;Judul:&lt;/label&gt;
        &lt;!-- Tetap pertahankan isian bila formulir salah (old field) --&gt;
        &lt;input type="text" name="title" value="&lt;?= e(old('title')) ?&gt;"&gt;
    
        &lt;!-- Cetak teks peringatan merah jika error `title` eksis --&gt;
        &lt;?php if ($error = errors('title')): ?&gt;
            &lt;div style="color:red;"&gt;&lt;?= $error ?&gt;&lt;/div&gt;
        &lt;?php endif; ?&gt;

        &lt;button type="submit"&gt;Kirim&lt;/button&gt;
    &lt;/form&gt;</code></pre>

<?php elseif ($activeLine === 'authentication'): ?>
    <h1>Authentication (Sistem Login)</h1>
    <p>Menangani login dan registrasi pengguna adalah hal yang mutlak. Lunox Backfire menyediakan perancah (<i>Facade</i>)
        <code>Auth</code> yang langsung bisa digunakan mendeteksi sesi aktif.</p>

    <h2>Registrasi & Hashing Password</h2>
    <p>Sebelum pengguna bisa login, mereka harus mendaftar. Selalu gunakan fungsi bawaan PHP <code>password_hash()</code>
        untuk mengamankan kata sandi sebelum masuk database:</p>
    <pre><code>public function register(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = new User();
        $user->email = $validated['email'];
        // Hashing password dengan algoritma BCRYPT yang super aman
        $user->password = password_hash($validated['password'], PASSWORD_DEFAULT);
        $user->save();

        return redirect('/login');
    }</code></pre>

    <h2>Login (Authenticating Users)</h2>
    <p>Gunakan method <code>Auth::attempt</code> untuk memeriksa kecocokan email dan password. Secara asali, sistem ini
        mencari model <code>User</code> dengan kolom <code>email</code> dan <code>password</code>:</p>
    <pre><code>use LunoxHoshizaki\Auth\Auth;

    if (Auth::attempt(['email' => $email, 'password' => $password])) {
        // Jika benar, login berhasil dan sesi tercipta.
        return redirect('/dashboard');
    } else {
        // Login gagal, kembalikan ke form...
    }</code></pre>

    <h2>Mengecek & Mengambil Data User Login</h2>
    <p>Setelah login, kamu dapat memanggil data lengkap dari pengguna yang sedang berlalu-lalang di aplikasimu melalui
        Facade <code>Auth</code>:</p>
    <pre><code>// Mengambil objek Model User yang sedang login
    $user = Auth::user();
    echo $user->email; // Cetak emailnya

    // Hanya mengambil ID-nya saja
    $id = Auth::id();

    // Cek apakah pengunjung adalah tamu (belum login) atau bukan
    if (Auth::check()) {
        // Pengguna sudah login!
    }</code></pre>

    <h2>Proteksi Middleware (Halaman Khusus Member)</h2>
    <p>Kamu dapat mengunci halaman tertentu (seperti Dashboard) dari akses tamu dengan menautkan <code>AuthMiddleware</code>
        di router:</p>
    <pre><code>Router::get('/profile', [ProfileController::class, 'show'])
        ->middleware(LunoxHoshizaki\Security\AuthMiddleware::class);</code></pre>

<?php elseif ($activeLine === 'helpers'): ?>
    <h1>Global Helpers & Sesi</h1>
    <p>Backfire menyediakan fungsi-fungsi praktis (<i>helpers</i>) global untuk mempercepat proses penulisan kodemu,
        terutama di area Tampilan (<i>Views</i>).</p>

    <ul>
        <li><code>old('field_name')</code> : Menarik kembali teks inputan pengguna dari sesi sebelumnya apabila proses
            formulir gagal atau terkena <i>Error Validation</i>, agar pengguna tidak perlu mengetik ulang dari awal.</li>
        <li><code>errors('field_name')</code> : Mengambil teks spesifik pesan kesalahan validasi.</li>
        <li><code>csrf_field()</code> : Wajib dipasang di dalam semua formulir HTML dengan <i>method</i> POST/PUT/DELETE.
            Fungsi ini me-<i>render</i> token anti-hijack untuk melindungi website.</li>
        <li><code>e($string)</code> : Men-<i>sanitize</i> dan meng-<i>escape</i> karakter HTML untuk mencegah serangan
            peretas (XSS). Selalu gunakan ini setiap kali mencetak output tidak terpercaya!</li>
        <li><code>asset('css/style.css')</code> : Membantu memosisikan direktori <i>root</i> ke URL di folder
            <code>public/</code>.</li>
    </ul>

    <p>Sistem dibelakang fungsi-fungsi ini dipegang oleh <code>SessionManager</code> berbasis memori Cookie yang sepenuhnya
        terenkripsi secara aman (<i>Encrypted</i>).</p>

<?php elseif ($activeLine === 'migrations'): ?>
    <h1>Database Migrations & Seeding</h1>
    <p><i>Migrations</i> bisa diibaratkan seperti *Version Control System* (seperti Git) tetapi untuk skema database. Kamu
        bisa merancang dan membagikan struktur tabel antartim tanpa harus saling melempar file SQL buatan.</p>

    <h2>Siklus Pembuatan Tabel</h2>
    <p>Untuk membuat file kerangka tabel (Migration), gunakan perintah CLI Backfire:</p>
    <pre><code>php backfire make:migration create_flights_table</code></pre>
    <p>File ini akan tersimpan otomatis di direktori <code>database/migrations/</code> dengan prefiks waktu eksaknya. Buka
        file tersebut lalu atur kolom yang kamu mau melalui <code>Schema::create</code> dan <code>Blueprint</code>.</p>

    <h2>Contoh Skrip Migrasi (Struktur Tabel)</h2>
    <pre><code>use LunoxHoshizaki\Database\Schema\Schema;
    use LunoxHoshizaki\Database\Schema\Blueprint;

    public function up()
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id(); // Membuat Primary Key INT AUTO_INCREMENT
            $table->string('name', 100); // VARCHAR(100)
            $table->boolean('is_active'); // TINYINT(1)
            $table->timestamps(); // Menambahkan created_at dan updated_at
            $table->softDeletes(); // Menambahkan deleted_at
        });
    }</code></pre>

    <h2>Mengeksekusi Migrasi</h2>
    <p>Setelah selesai merancang file, ubah rencanamu menjadi tabel fisik di dalam database SQL dengan perintah:</p>
    <pre><code>php backfire migrate</code></pre>

    <h2>Database Seeding (Suntik Data Massal)</h2>
    <p>Saat masa pengembangan, biasanya kita butuh banyak sampel data palsu untuk diuji coba. <i>Seeders</i> adalah kelas
        untuk melakukan ini.</p>
    <pre><code>// 1. Buat kerangka Seeder
    php backfire make:seeder UserSeeder

    // 2. Tulis data eksekusi di app/database/seeders/UserSeeder.php

    // 3. Jalankan penyuntikkan
    php backfire db:seed</code></pre>

<?php elseif ($activeLine === 'orm'): ?>
    <h1>ORM Relationships (Relasi Antartabel)</h1>
    <p>Model Lunox Backfire mendukung pembuatan relasi otomatis untuk mempermudah pemanggilan data antar tabel yang saling
        terhubung (misal: 1 User memiliki banyak Post).</p>
    <h2>Relasi yang Didukung</h2>
    <ul>
        <li><code>hasOne(RelatedModel::class)</code>: Memiliki 1 relasi data.</li>
        <li><code>hasMany(RelatedModel::class)</code>: Memiliki banyak relasi data.</li>
        <li><code>belongsTo(RelatedModel::class)</code>: Dimiliki oleh tabel lain (Kebalikan `hasOne` / `hasMany`).</li>
        <li><code>belongsToMany(RelatedModel::class)</code>: Relasi banyak-ke-banyak (Membutuhkan tabel / <i>pivot</i>
            perantara).</li>
    </ul>
    <p><b>Contoh Penggunaan:</b></p>
    <pre><code>// Mengambil User dengan ID 1
    $user = User::find(1);

    // Mengambil semua Post milik User tersebut
    $posts = $user->hasMany(Post::class)->get();</code></pre>

<?php elseif ($activeLine === 'storage'): ?>
    <h1>File Storage (Penyimpanan File)</h1>
    <p>Kelas <code>Storage</code> menyediakan jembatan (API) simpel dan aman guna mengelola file di server lokalmu secara
        aman.</p>
    <pre><code>use LunoxHoshizaki\Storage\Storage;

    // Simpan file ke folder `public/storage`
    Storage::put('avatars/1.jpg', $fileDataBinary);

    // Dapatkan URL absolut publiknya
    $url = Storage::url('avatars/1.jpg');

    // Hapus file dari server
    Storage::delete('avatars/1.jpg');</code></pre>

<?php elseif ($activeLine === 'cli'): ?>
    <h1>Custom CLI Commands (Perintah Terminal Kustom)</h1>
    <p>Kalian bisa mengotomatisasi beberapa pekerjaan berat lewat skrip Backfire CLI.</p>
    <pre><code>// Buat file Command baru
    php backfire make:command SendEmails</code></pre>
    <p>Kode kerangka perintah akan masuk ke direktori <code>app/Console/Commands/SendEmails.php</code>. Buka dan masukkan
        logikamu di fungsi <code>handle()</code>. Jalankan kode kustommu dengan:</p>
    <pre><code>php backfire send:emails</code></pre>

<?php elseif ($activeLine === 'mail'): ?>
    <h1>Mailer System (Pengiriman Email)</h1>
    <p>Mengirim email tidak pernah semudah ini di Backfire dengan hadirnya kelas `Mail` dan `Mailable`.</p>
    <pre><code>use LunoxHoshizaki\Mail\Mail;

    // Kirimkan instance dari Class Mailable
    Mail::to('user@contoh.com')->send(new WelcomeEmail());</code></pre>

<?php elseif ($activeLine === 'cache'): ?>
    <h1>Cache System (Penyimpanan Sementara)</h1>
    <p>Ketika kamu punya kueri SQL super berat yang memperlambat website atau data pihak ke-3 (API) yang lambat dimuat, kamu
        dapat meminjam *Cache* berbasis file ini sebagai solusi:</p>
    <pre><code>use LunoxHoshizaki\Cache\Cache;

    // Simpan data kueri berat selama 1 Jam
    Cache::put('key_rahasia_data', 'NilaiData', 3600);

    // Panggil lagi data tersebut (Tarik default jika kedaluwarsa)
    $value = Cache::get('key_rahasia_data', 'default');</code></pre>

<?php elseif ($activeLine === 'events'): ?>
    <h1>Events & Listeners</h1>
    <p>Bebaskan kerumitan Controller utama dengan teknik Pemancar (*Publisher*) / Pendengar (*Subscriber*).</p>
    <pre><code>use LunoxHoshizaki\Events\Event;

    // Mengirimkan gelombang siaran Event ke semua Listener yang tertaut
    Event::dispatch(new UserRegistered($user));

    // Atur di tempat lain file/fungsi apa saja yang perlu dieksekusi ketika tertrigger UserRegistered
    Event::listen(UserRegistered::class, SendWelcomeEmail::class);</code></pre>

<?php elseif ($activeLine === 'security'): ?>
    <h1>Security & DDoS Protections</h1>
    <p>Lunox Backfire (Phase 6) memperkenalkan proteksi berlapis kelas Enterprise ke dalam aplikasimu untuk melawan serangan
        web otomatis modern.</p>

    <h2>Rate Limiting (Anti-DDoS / Brute-Force)</h2>
    <p>Middleware <code>ThrottleRequests</code> digunakan membatasi IP yang usil. Secara default ia memblokir pengguna bila
        mengirimkan > 60 Request / per menit.</p>
    <pre><code>use LunoxHoshizaki\Security\ThrottleRequests;

    Router::get('/api/data', [ApiController::class, 'index'])
        ->middleware(ThrottleRequests::class);</code></pre>
    <p>Aplikasi akan memutus sambungan IP tersebut cepat kilat dengan pesan HTTP <code>429 Too Many Requests</code>.</p>

    <h2>Cross-Site Scripting (XSS)</h2>
    <p>Pastikan untuk selalu meloloskan output apapun dari hasil input pengguna melalui Helper <code>e()</code> yang sudah
        disematkan dalam inti Backfire:</p>
    <pre><code>&lt;!-- BAD: Rentan serangan Injeksi XSS Code --&gt;
    &lt;?= $user->bio ?&gt;

    &lt;!-- GOOD: Karakter Berbahaya telah dibungkam  --&gt;
    &lt;?= e($user->bio) ?&gt;</code></pre>

    <h2>Cross-Origin Resource Sharing (CORS)</h2>
    <p>Ingin API diakses oleh Framework *Frontend* keren seperti React/Vue dari *Domain* berbeda? Sematkan
        <code>CorsMiddleware</code> di Router untuk menghilangkan blokirannya aman.</p>
    <pre><code>use LunoxHoshizaki\Security\CorsMiddleware;

    Router::group(['prefix' => '/api', 'middleware' => [CorsMiddleware::class]], function () {
        Router::get('/users', [ApiController::class, 'users']);
    });</code></pre>

    <h2>Secure HTTP Headers</h2>
    <p>Middleware <code>SecureHeadersMiddleware</code> menaburkan lapisan ajaib penolak *Clickjacking* dan penolakan injeksi
        *MIME sniffing* dari peramban lawas.</p>
    <pre><code>use LunoxHoshizaki\Security\SecureHeadersMiddleware;

    // Pasang Global pada website rahasiamu
    Router::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(SecureHeadersMiddleware::class);</code></pre>

<?php elseif ($activeLine === 'csrf'): ?>
    <h1>CSRF Protection</h1>
    <p>Semua aplikasi web rentan terhadap serangan <b>Cross-Site Request Forgery (CSRF)</b> di mana situs web jahat
        memalsukan permintaan atas nama pengguna yang sudah login ke aplikasimu.</p>

    <h2>Bagaimana Backfire Melindungimu?</h2>
    <p>Lunox Backfire secara otomatis membuatkan "CSRF Token" unik untuk setiap sesi pengguna yang aktif. Keamanan berlapis
        ini memastikan bahwa setiap permintaan yang mengubah state (POST, PUT, DELETE, PATCH) benar-benar berasal dari
        aplikasimu sendiri.</p>

    <h2>Menggunakan CSRF Protection pada Formulir</h2>
    <p>Setiap kali kamu mendefinisikan sebuah form HTML yang menggunakan metode <code>POST</code>, <code>PUT</code>,
        <code>PATCH</code>, atau <code>DELETE</code>, kamu wajib menyertakan CSRF Token. Jika tidak, permintaan tersebut
        akan ditolak oleh sistem keamanan (HTTP 419 Page Expired).</p>
    <p>Gunakan global helper <code>csrf_field()</code> untuk me-render token ke dalam input form tersembunyi dengan mudah:
    </p>
    <pre><code>&lt;form action="/posts/simpan" method="POST"&gt;
        &lt;!-- Wajib ditaruh di dalam form! --&gt;
        &lt;?= csrf_field() ?&gt;

        &lt;label&gt;Judul Tulisan:&lt;/label&gt;
        &lt;input type="text" name="judul"&gt;
    
        &lt;button type="submit"&gt;Posting&lt;/button&gt;
    &lt;/form&gt;</code></pre>

    <h2>CSRF Token pada AJAX (Axios / Fetch)</h2>
    <p>Jika kamu membangun aplikasi berbasis *Frontend API* (menggunakan JavaScript murni atau Framework UI), kamu harus
        memastikan token CSRF selalu dikirim bersamaan dengan permintaan AJAX.</p>
    <p>Cara praktisnya adalah meletakkan token asli di dalam meta tag HTML <code>&lt;head&gt;</code>:</p>
    <pre><code>&lt;meta name="csrf-token" content="&lt;?= $_SESSION['csrf_token'] ?? '' ?&gt;"&gt;</code></pre>
    <p>Kemudian, atur *library* HTTP-mu (contoh: Axios) untuk secara otomatis mengekstrak dan mengirimkannya ke Header
        khusus <code>X-CSRF-TOKEN</code>:</p>
    <pre><code>// Contoh pada library Axios
    let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;</code></pre>

<?php elseif ($activeLine === 'request-response'): ?>
    <h1>Request & Response</h1>
    <p>Interaksi antara pengguna dan aplikasimu pada dasarnya adalah menerima <b>Request (Permintaan)</b> dan mengirimkan
        <b>Response (Tanggapan)</b>.</p>

    <h2>Mengambil Data Request</h2>
    <p>Kelas <code>LunoxHoshizaki\Http\Request</code> memungkinkanmu membedah setiap data yang datang dari pengguna, baik
        berupa parameter URL (GET) maupun inputan formulir (POST).</p>
    <pre><code>public function store(Request $request)
    {
        // Mengambil satu nilai spesifik pembungkusan GET & POST
        $name = $request->input('name');
    
        // Mengambil nilai default jika parameter tidak ditemukan
        $age = $request->input('age', 18);
    
        // Mengambil semua data utuh berbentuk Array
        $data = $request->all();
    }</code></pre>

    <h2>Merakit Custom Response</h2>
    <p>Meskipun Backfire secara otomatis merubah kembalian <i>string</i> menjadi HTTP Response 200, kamu kadang-kadang
        memerlukan kontrol yang lebih dalam (seperti mengembalikan format JSON API dengan header khusus).</p>
    <p>Gunakan kelas <code>LunoxHoshizaki\Http\Response</code>:</p>
    <pre><code>use LunoxHoshizaki\Http\Response;

    public function getJsonInfo()
    {
        $response = new Response();
        $response->setContent(json_encode([
            'status' => 'success', 
            'version' => '1.11.0'
        ]));
    
        // Set Header untuk JSON dan kembalikan kode 201 Created
        $response->setHeader('Content-Type', 'application/json');
        $response->setStatusCode(201);
    
        return $response;
    }</code></pre>

<?php elseif ($activeLine === 'errors'): ?>
    <h1>Error Handling (Penanganan Eksepsi)</h1>
    <p>Ketika ada kejanggalan atau kegagalan yang tidak diinginkan di dalam produksi, kamu tentu tidak ingin menampilkan
        jejak kodemu (*stack trace*) berwarna oranye ke mata publik yang mengancam privasi.</p>

    <h2>Tampilan Error Otomatis</h2>
    <p>Setiap eksepsi tak tertangani (<i>Unhandled Exception</i>) yang dilempar dari aplikasi akan ditangkap lekat oleh
        <code>Application</code> inti dan dialihkan secara elegan untuk me-<i>render</i> file View bawaan di
        <code>resources/views/errors/error.php</code>.</p>
    <p>Backfire otomatis menangkap kesalahan spesifik seperti:</p>
    <ul>
        <li><b>404 Not Found</b>: Halaman tak ditemukan saat tidak ada rute URL yang cocok atau gagal menemukan data model
            via fungsi <code>find()</code>.</li>
        <li><b>500 Internal Error</b>: Segala eksekusi fatal PHP yang gagal di tengah proses.</li>
        <li><b>419 Page Expired</b>: Penolakan ketika Token CSRF absen atau tidak valid.</li>
        <li><b>429 Too Many Requests</b>: Mencegat serangan spam (*Rate Limiter*).</li>
    </ul>
    <p>Kamu bisa mengkustomisasi seluruh tampilan dasar Error ini di <code>resources/views/errors/error.php</code> layaknya
        CSS / HTML biasa menyesuaikan gaya visual websitemu.</p>

<?php elseif ($activeLine === 'env'): ?>
    <h1>Environment Configuration (.env)</h1>
    <p>File konfigurasi sangatlah berbahaya jika ditebar bebas di ruang publik GitHub, khususnya informasi berharga seperti
        kata sandi masuk Database Server, API Keys rahasia, atau kredensial peladen Email SMTP.</p>

    <h2>Menyimapn Variabel Lingkungan</h2>
    <p>Lunox dilengkapi sistem pembaca format <code>.env</code>. Kamu disarankan untuk menyimpan rahasia konfigurasi di file
        <code>.env</code> di akar repositori.</p>
    <pre><code>APP_NAME="Lunox Backfire"
    APP_ENV=local
    APP_DEBUG=true

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=lunox_lite
    DB_USERNAME=root
    DB_PASSWORD=secret</code></pre>
    <p>Karena file ini mendasar sifatnya, file <code>.env</code> sudah otomatis diblokir ke repositori via
        <code>.gitignore</code>.</p>

    <h2>Mengakses Konfigurasi</h2>
    <p>Setelah tercatat di sana, variabel ini dilebur ke dalam memori global PHP. Gunakan fungsi internal
        <code>getenv()</code> atau baca array superglobal <code>$_ENV</code> di file konfigurasi atau controllermu:</p>
    <pre><code>// Mengakses password database
    $dbPass = $_ENV['DB_PASSWORD'] ?? '';

    if ($_ENV['APP_ENV'] === 'production') {
        // Jalankan mode rilis
    }</code></pre>

<?php elseif ($activeLine === 'artisan'): ?>
    <h1>Backfire Console</h1>
    <p>Backfire adalah aplikasi antarmuka baris perintah (CLI) pendamping asli Lunox Backfire. Script <code>backfire</code>
        berada tepat di folder utama website kamu menantimu memberikan instruksi ajaib instan.</p>

    <h2>Perintah yang Tersedia</h2>
    <p>Ketik ini di konsol/terminalmu untuk melihat apa saja yang Backfire mampu otomatisasikan:</p>
    <pre><code>php backfire</code></pre>

    <p>Beberapa contoh pekerjaan berulang yang dapat dihasilkan dalam detik oleh CLI Backfire:</p>
    <ul>
        <li><code>php backfire make:controller AuthController</code></li>
        <li><code>php backfire make:model Product</code></li>
        <li><code>php backfire make:middleware VerifyToken</code></li>
        <li><code>php backfire make:migration add_users_table</code></li>
        <li><code>php backfire migrate</code></li>
        <li><code>php backfire db:seed</code></li>
        <li><code>php backfire key:generate</code></li>
        <li><code>php backfire cache:clear</code></li>
        <li><code>php backfire serve</code></li>
    </ul>

<?php endif; ?>

<?php View::endsection(); ?>
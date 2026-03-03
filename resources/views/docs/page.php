<?php use LunoxHoshizaki\View\View; ?>
<?php View:: extends('layouts.docs'); ?>

<?php View::section('docs-content'); ?>

<?php if ($activeLine === 'installation'): ?>
    <h1>Installation & Architecture</h1>
    <p>Halo coder! Selamat datang di Lunox Backfire. Ini adalah framework PHP eksperimental yang sengaja dibikin super
        ngebut, tapi tetep ngasih kenyamanan nulis kode kayak di framework gede sekelas Laravel. <i>Guide</i> ini bakal
        bantu kamu paham konsep dasarnya biar siap bikin aplikasi web kekinian yang kenceng dan aman.</p>

    <h2>Cara Instal Framework</h2>
    <p>Biar gampang, ikutin aja langkah-langkah <i>clone</i> dan *setup* awal dari skeleton GitHub
        <b>lunoxhoshizaki-lite</b> ini:
    </p>

    <p><b>1. Clone Repository</b></p>
    <p>Kalo kamu pengen nama folder <i>project</i>-mu beda dari nama aslinya (misal mau dinamain <code>toko-online</code>
        atau <code>my-app</code>), tambahin aja namanya di akhir perintah <code>git clone</code> kayak gini:</p>
    <pre><code># Format: git clone [URL_REPO] [NAMA_FOLDER_KAMU]
git clone https://github.com/lunox-61/lunoxhoshizaki-lite.git toko-online
cd toko-online</code></pre>

    <p><b>2. Install Dependencies Pihak Ketiga</b></p>
    <p>Sekarang jalanin perintah Composer buat nge-download semua pustaka pendukung (termasuk buat baca file <i>.env</i>):
    </p>
    <pre><code>composer install</code></pre>

    <p><b>3. Dump Autoload</b></p>
    <p>Biar semua <i>class</i> PHP di framework ini bisa saling kenal dan keload dengan bener, jalanin ini:</p>
    <pre><code>composer dump-autoload -o</code></pre>

    <p><b>4. Bikin File Environment</b></p>
    <p>Sistem Backfire baca konfigurasi sensitif dari file `.env`. Kita copy dulu dari file contoh yang udah ada:</p>
    <pre><code>cp .env.example .env</code></pre>

    <h2>Syarat & Kebutuhan (Requirements)</h2>
    <p>Karena framework ini pake fitur-fitur PHP lumayan baru, pastiin komputermu udah punya:</p>
    <ul>
        <li>PHP >= 8.1 (versi lebih baru makin bagus)</li>
        <li>Tentu aja Ekstensi PDO & OpenSSL diaktifin</li>
        <li>Database buat nyimpen data (MySQL / SQLite / MariaDB)</li>
    </ul>

    <h2>Struktur Folder Inti</h2>
    <p>Abis diinstal, kamu bakal ngeliat folder-folder penting ini nih:</p>
    <ul>
        <li><b><code>app/</code></b> : Ini rumah buat aplikasimu. Semua <i>logic</i> utama (Models, Controllers, Events)
            ditaroh di sini.</li>
        <li><b><code>database/</code></b> : Tempat nyimpen file Migrations (buat bikin tabel database otomatis) dan Seeders
            (buat nyuntik data boongan buat testing).</li>
        <li><b><code>public/</code></b> : Pintu masuk utamanya (`index.php`). File-file CSS, JS, sama Gambar juga ditaroh di
            sini aja.</li>
        <li><b><code>resources/views/</code></b> : Nah, ini tempat naroh kode tampilan webnya (HTML / UI).</li>
        <li><b><code>routes/</code></b> : Tempat nentuin list URL/link apa aja yang ada di websitemu (di
            <code>web.php</code> atau <code>api.php</code>).
        </li>
        <li><b><code>src/</code></b> : <i>Ini Mesin Utama Framework-nya.</i> Mending jangan diotak-atik ya, kecuali kamu
            niat banget pengen nyumbang kode ke <i>core</i>-nya!</li>
    </ul>

    <h2>Jalanin Server di Laptopmu</h2>
    <p>Pas lagi *development*, kamu nggak perlu repot-repot setup server berat kayak Apache/XAMPP. Backfire udah ada server
        internal bawaan yang sat-set. Ketik aja ini di terminal:</p>
    <pre><code>php backfire serve</code></pre>
    <p>Boom! Website kamu udah bisa diakses langsung lewat <code>http://localhost:8000</code>.</p>

<?php elseif ($activeLine === 'routing'): ?>
    <h1>Routing (Alur URL)</h1>
    <p>Routing ini ibarat gerbang utama aplikasimu. Di sinilah kamu daftarin link/URL apa aja yang valid buat diakses
        pengunjung, mau diarahkan ke file tampilan yang mana, atau mau ngeksekusi <i>logic</i> apa pas ditekan.</p>

    <h2>Basic Web Routing</h2>
    <p>Semua URL/rute utama web kita simpen di file <code>routes/web.php</code>. Bikin *route* itu gampang banget, contohnya
        gini buat ngembaliin teks atau manggil halaman tampilan (*View*):</p>
    <pre><code>use LunoxHoshizaki\Routing\Router;

Router::get('/halo', function () {
    return 'Halo Dunia! Welcome!';
});</code></pre>

    <h2>Nangkep Data dari URL (Parameter)</h2>
    <p>Kadang kita pengen bikin URL yang dinamis, misalnya URL profil user yang isinya beda-beda ID-nya. Gampang, tinggal
        pake tanda kurung kurawal <code>{ }</code> aja:</p>
    <pre><code>Router::get('/user/{id}', function ($request, $id) {
    return 'Melihat Profil Pengguna dengan ID: ' . $id;
});</code></pre>

    <h2>Menangani POST, PUT, DELETE HTTP</h2>
    <p>Untuk nerima data hasil *submit* dari form (formulir) HTML atau kalo kamu bikin REST API, kamu bisa pake metode yang
        lain selain GET:</p>
    <pre><code>Router::post('/user/simpan', [UserController::class, 'store']);
Router::put('/user/{id}/update', [UserController::class, 'update']);
Router::delete('/user/{id}/hapus', [UserController::class, 'destroy']);</code></pre>

    <h2>Ngelompokin URL (Grup & Middleware)</h2>
    <p>Kalo kamu punya banyak rute yang butuh perlindungan (misalnya, cuma boleh diakses kalo udah login/admin), daripada
        nulis satu-satu, mending masukin aja ke dalem grup kayak gini:</p>
    <pre><code>Router::group(['prefix' => '/admin', 'middleware' => [AuthMiddleware::class]], function () {
    Router::get('/dashboard', [AdminController::class, 'index']); // Jadinya: /admin/dashboard
    Router::get('/setting', [AdminController::class, 'setting']); // Jadinya: /admin/setting
});</code></pre>

<?php elseif ($activeLine === 'middleware'): ?>
    <h1>Middleware (Si Tukang Jaga Pintu)</h1>
    <p><i>Middleware</i> itu ibarat satpam yang ngecekin setiap <i>request</i> yang masuk atau keluar dari web kamu. Contoh
        paling gampangnya, Backfire udah nyediain middleware buat ngecek apakah pengunjung udah login atau belum sebelum
        mereka bisa buka halaman tertentu.</p>

    <h2>Bikin Middleware Sendiri</h2>
    <p>Mau bikin middleware baru? Nggak usah ngetik dari nol, pake aja perintah CLI `make:middleware` ini:</p>
    <pre><code>php backfire make:middleware CheckAge</code></pre>
    <p>Nanti bakal muncul file <i>class</i> `CheckAge` di dalem folder `app/Http/Middleware` (atau `app/Middleware`
        tergantung *setup*-mu).</p>

    <p>Nah, di dalem middleware itu, kamu tinggal nulis aturan utamanya di fungsi `handle`. Contohnya nih, kita mau nolak
        bocil (di bawah 18 tahun) buat masuk:</p>
    <pre><code>namespace App\Middleware;

use LunoxHoshizaki\Http\Request;

class CheckAge
{
    public function handle(Request $request, \Closure $next)
    {
        if ($request->input('age') < 18) {
            // Kalo di bawah umur, tendang balik ke home!
            return redirect('/home');
        }

        // Kalo aman, silakan masuk~
        return $next($request);
    }
}</code></pre>

    <h2>Daftarin & Pake Middleware</h2>
    <p>Kalo file-nya udah jadi, kamu bisa langsung pasang satpam ini ke *route* tertentu di `routes/web.php` atau
        `routes/api.php`.</p>
    <pre><code>use App\Middleware\CheckAge;

// Pasang satpam cuma di satu halaman ini aja
Router::get('/khusus-dewasa', function () {
    return 'Selamat datang di area khusus!';
})->middleware(CheckAge::class);

// Atau sekalian pasang satpam di satu blok grup halaman
Router::group(['prefix' => '/admin', 'middleware' => [CheckAge::class]], function () {
    Router::get('/dashboard', [AdminController::class, 'index']); // Aman!
});</code></pre>

<?php elseif ($activeLine === 'controllers'): ?>
    <h1>Controllers (Si Pengatur Lalu Lintas)</h1>
    <p>Nulis semua kode ruwet di dalem file <code>web.php</code> itu ide buruk, nanti *file*-nya kepanjangan dan pusing
        bacanya. Di sinilah <b>Controllers</b> beraksi! Controller itu semacam tempat buat ngumpulin *logic-logic* yang
        sejenis biar rapi.</p>

    <h2>Bikin Controller Baru</h2>
    <p>Lagi-lagi, pake bantuan CLI Backfire aja biar cepet:</p>
    <pre><code>php backfire make:controller UserController</code></pre>
    <p>Selesai! File baru langsung nongol di <code>app/Controllers/UserController.php</code>.</p>

    <h2>Gimana Sih Bentuk Controller Itu?</h2>
    <p>Biasanya, Controller itu tugasnya nerima *Request* (permintaan user), ngolah datanya, terus ngasih balikan berupa
        HTML (<i>View</i>) atau format JSON (kalo bikin API):</p>
    <pre><code>namespace App\Controllers;

use LunoxHoshizaki\Http\Request;
use LunoxHoshizaki\View\View;
use LunoxHoshizaki\Http\Response;

class UserController
{
    // Contoh nampilin halaman HTML profil user
    public function show(Request $request, $id)
    {
        return View::make('user.profile', ['id' => $id]);
    }

    // Contoh ngebalikin data JSON (Mantap buat bikin API Frontend)
    public function getJson(Request $request)
    {
        $response = new Response();
        $response->setContent(json_encode(['status' => 'success', 'data' => 'Data Rahasia']));
        $response->setHeader('Content-Type', 'application/json');
        return $response;
    }
}</code></pre>

    <h2>Nyambungin Controller ke Route</h2>
    <p>Cara manggilnya di file rute gampang banget, tinggal tulis nama *Class Controller*-nya sama nama fungsinya di dalem
        <i>Array</i>:
    </p>
    <pre><code>Router::get('/user/{id}', [UserController::class, 'show']);</code></pre>

<?php elseif ($activeLine === 'views'): ?>
    <h1>Views (Halaman Tampilan)</h1>
    <p><i>Views</i> ini ibarat wajah aplikasimu. Tugas utamanya misahin kode PHP yang ribet sama kode HTML web biar
        desainermu nggak pusing. Semua file tampilan dikumpulin di folder <code>resources/views</code>.</p>

    <h2>Manggil Tampilan ke Layar</h2>
    <p>Kamu cuma butuh method <code>View::make</code> di dalem Controller buat muculin filenya (nggak usah nulis akhiran
        <code>.php</code> ya):
    </p>
    <pre><code>// Bakal manggil file resources/views/greeting.php
return View::make('greeting', ['nama' => 'Budi']);</code></pre>
    <p>Terus di dalem file <code>greeting.php</code>, bebas deh kamu cetak datanya (Ingat: usahain selalu pake helper
        <code>e()</code> biar webmu kebal serangan XSS!):
    </p>
    <pre><code>&lt;h1&gt;Halo, &lt;?= e($nama) ?&gt;!&lt;/h1&gt;</code></pre>

    <h2>Layouts (Biar Gak Nulis HTML Berulang)</h2>
    <p>Pasti males kan nulis tag <code>&lt;head&gt;</code> atau navigasi menu berulang-ulang tiap ganti halaman? Tenang,
        pake sistem *Template Inheritance* aja!</p>

    <p><b>1. Bikin Master Layout Dulu (misal di <code>resources/views/layouts/app.php</code>):</b></p>
    <pre><code>&lt;html&gt;
&lt;head&gt;&lt;title&gt;Aplikasi Keren&lt;/title&gt;&lt;/head&gt;
&lt;body&gt;
    &lt;!-- Ini titik tempat konten halaman bakal dimasukin --&gt;
    &lt;?= $this-&gt;yieldContent('content') ?&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>

    <p><b>2. Pake Layoutnya di Halaman Lain:</b></p>
    <pre><code>&lt;?php View::extends('layouts.app'); ?&gt;

&lt;?php View::section('content'); ?&gt;
    &lt;h1&gt;Hai, ini spesifik muncul di bagian dalem body lho!&lt;/h1&gt;
&lt;?php View::endsection(); ?&gt;</code></pre>

<?php elseif ($activeLine === 'database'): ?>
    <h1>Database & Models</h1>
    <p>Lunox Backfire udah dibekali fitur <i>ActiveRecord</i> super simpel yang bakal ngurusin query database otomatis, jadi
        kamu nggak perlu pusing ngetik perintah SQL manual panjang-panjang.</p>

    <h2>Setting Koneksi Database</h2>
    <p>Sebelum mulai main data, pastiin file <code>.env</code> kamu udah diisi info login yang bener sesuai sama server
        MySQL di komputermu:</p>
    <pre><code>DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lunox_lite
DB_USERNAME=root
DB_PASSWORD=rahasia</code></pre>

    <h2>Bikin Model Buat Tabel</h2>
    <p>Biasanya, tiap tabel di database itu diwakilin sama satu <b>Model</b>. Coba jalanin CLI Backfire ini:</p>
    <pre><code>php backfire make:model Product</code></pre>
    <p>Nah, otomatis Backfire bakal nyari tabel yang namanya jamak (bahasa Inggris) dari nama Modelnya (misal: <i>class</i>
        `Product` bakal auto-nyambung ke tabel <code>products</code>).</p>

    <h2>Operasi CRUD (Nambah, Baca, Edit, Hapus)</h2>
    <p>Saking gampangnya, nyimpen baris data baru ke tabel tuh cuma gini doang:</p>
    <pre><code>// CREATE: Buat nabung data baru
$produk = new Product();
$produk->name = 'Buku Panduan Ajaib';
$produk->price = 150000;
$produk->save();

// READ: Ngambil data dari tabel
$satuProduk = Product::find(1); // Cuma ngambil yang ID-nya 1
$semuaProduk = Product::where('price', '>', 50000)->get();

// UPDATE: Ngedit / ngerubah data lama
$produkLama = Product::find(1);
$produkLama->price = 100000;
$produkLama->save();

// DELETE: Ngehapus selamanya
$produkHapus = Product::find(2);
$produkHapus->delete();</code></pre>

    <h2>Lagi Mau Kueri Ribet? Pake Advanced Query Builder!</h2>
    <p>Kalo kamu butuh narik data spesifik pake *sorting*, *limit*, sampe dibikin halaman-halaman otomatis (Paginasi) ala
        Bootstrap 5:</p>
    <pre><code>// Tarik data pake urutan dan batas
$terlaris = Product::query()
    ->where('stock', '>', 0)
    ->orderBy('sold_count DESC')
    ->limit(10)
    ->get();

// Otomatis mecah data jadi 15 barang per halaman
$semuaProduk = Product::paginate(15);
</code></pre>

    <p>Terus panggil fungsi jitu ini di file <i>View</i> kamu buat ngerender tombol-tombol halamannya:</p>
    <pre><code>&lt;!-- Nampilin navigasi halaman (<< 1 2 3 >>) otomatis --&gt;
&lt;?= $semuaProduk-&gt;links() ?&gt;</code></pre>

<?php elseif ($activeLine === 'validation'): ?>
    <h1>Validation & Forms (Formulir Aman)</h1>
    <p>Jangan pernah langsung percaya sama data yang dikirim user! Backfire punya fitur validasi bawaan di objek
        <code>Request</code> buat nyaring isian <i>form</i> sebelum diproses ke database.
    </p>

    <h2>Filter Request Masuk</h2>
    <p>Kamu cuma perlu nentuin *rules* (aturan) apa aja yang wajib ditepatin sama inputan *user*:</p>
    <pre><code>public function store(Request $request)
{
    // Kalo ada yang kosong/salah, prosesnya langsung stop dan dibalikin ke form sebelumnya
    $validated = $request->validate([
        'title' => 'required|max:255',
        'body'  => 'required',
        'email' => 'required|email'
    ]);

    // Kalo lolos validasi, baru lanjut simpen...
    $post = new Post();
    $post->title = $validated['title'];
    $post->save();

    return redirect('/posts');
}</code></pre>

    <h2>Munculin Peringatan Merah di HTML</h2>
    <p>Kalo *user* salah ngisi, sistem langsung nge-<i>flash</i> notifikasi pesannya ke Sesi. Pake Helper
        <code>errors()</code> dan <code>old()</code> biar form-mu tetep estetik dan nggak ngeselin buat pengunjung:
    </p>
    <pre><code>&lt;form action="/simpan" method="POST"&gt;
    &lt;!-- JANGAN LUPA! Selalu taruh anti-hack token ini di semua form! --&gt;
    &lt;?= csrf_field() ?&gt;

    &lt;label&gt;Judul:&lt;/label&gt;
    &lt;!-- Biar user nggak capek ngetik ulang kalo filenya salah --&gt;
    &lt;input type="text" name="title" value="&lt;?= e(old('title')) ?&gt;"&gt;

    &lt;!-- Misal error `title` muncul, kita cetak teks merah --&gt;
    &lt;?php if ($error = errors('title')): ?&gt;
        &lt;div style="color:red;"&gt;Ups! &lt;?= $error ?&gt;&lt;/div&gt;
    &lt;?php endif; ?&gt;

    &lt;button type="submit"&gt;Kirim Data&lt;/button&gt;
&lt;/form&gt;</code></pre>

<?php elseif ($activeLine === 'authentication'): ?>
    <h1>Authentication (Sistem Login)</h1>
    <p>Bikin fitur *login* dan *register* dari nol itu repot. Di Backfire, ada *Facade* `Auth` sederhana yang bisa dipakai
        buat ngecek status *login* *user*.</p>

    <h2>Daftar Member & Hashing Password</h2>
    <p>Penting nih: jangan pernah nyimpen password mentah ke database. Selalu *hash* dulu pake fungsi bawaan
        <code>password_hash()</code> bawaan PHP biar lebih aman:
    </p>
    <pre><code>public function register(Request $request)
{
    $validated = $request->validate([
        'email'    => 'required|email',
        'password' => 'required'
    ]);

    $user = new User();
    $user->email = $validated['email'];
    // Hash password-nya pake algoritma default PHP (BCRYPT)
    $user->password = password_hash($validated['password'], PASSWORD_DEFAULT);
    $user->save();

    // Habis daftar, arahkan ke halaman login
    return redirect('/login');
}</code></pre>

    <h2>Coba Masuk (Auth Attempt)</h2>
    <p>Kamu bisa pakai fungsi <code>Auth::attempt</code> buat nyocokin *email* dan *password* dari *form* dengan data di
        *database*. Secara bawaan, ini bakal baca dari tabel <code>User</code>:</p>
    <pre><code>use LunoxHoshizaki\Auth\Auth;

if (Auth::attempt(['email' => $email, 'password' => $password])) {
    // Kalo cocok, sesi login dibuat, lanjut ke halaman utama
    return redirect('/dashboard');
} else {
    // Kalo gagal, balikin lagi dengan pesan error
}</code></pre>

    <h2>Ngecek Status Pengguna</h2>
    <p>Setelah *user* login, kamu bisa panggil datanya di mana aja (Controller atau View) pake *Facade* <code>Auth</code>:
    </p>
    <pre><code>// Ngambil instance model User yang lagi login
$user = Auth::user();
echo $user->email; // Contoh nampilin email

// Atau ngambil ID-nya aja
$id = Auth::id();

// Buat ngecek simpel si user ini udah login belum
if (Auth::check()) {
    // Kalau udah
}</code></pre>

    <h2>Kunci Halaman Pake Middleware</h2>
    <p>Biar halaman rahasia (misal dasbor admin) nggak bisa diintip tamu, tempel aja gembok <code>AuthMiddleware</code> di
        file *route* kamu:</p>
    <pre><code>Router::get('/profile', [ProfileController::class, 'show'])
->middleware(LunoxHoshizaki\Security\AuthMiddleware::class);</code></pre>

<?php elseif ($activeLine === 'helpers'): ?>
    <h1>Global Helpers & Sesi</h1>
    <p>Backfire punya beberapa fungsi manja (*helpers*) yang bisa dipanggil kapan aja di manapun tanpa basa-basi *import
        class* (apalagi pas di file View HTML).</p>

    <ul>
        <li><code>old('field_name')</code> : Mengembalikan tulisan yang sempet diketik user pas mereka disuruh balik ke form
            gara-gara salah ngisi (biar mereka seneng nggak perlu ketik ulang dari nol).</li>
        <li><code>errors('field_name')</code> : Manggil pesan *error* pas input *form* ditolak.</li>
        <li><code>csrf_field()</code> : Tag wajib buat ditaruh di dalem form POST/PUT. Ini tameng gaib (Token) buat nendang
            *hacker* iseng yang mau manipulasi isian website-mu.</li>
        <li><code>e($string)</code> : Tameng lapis dua! Ini bakal nyapu bersih semua kode jahat (XSS) di teks sebelum nampil
            ke layar. <b>Wajib pake ini tiap nyetak data nggak jelas dari *user*!</b></li>
        <li><code>asset('css/style.css')</code> : Perpendek nulis jalur (URL) *absolute* buat manggil gambar atau file CSS
            dari folder <code>public/</code>.</li>
    </ul>

    <p>Di balik layar, hal-hal keren tentang memori <i>flash</i> dan sesi ini dikelola sama tukang catat
        <code>SessionManager</code> yang keamanannya (kriptografi cookie-nya) dijaga super ketat.
    </p>

<?php elseif ($activeLine === 'migrations'): ?>
    <h1>Database Migrations & Seeding</h1>
    <p><i>Migrations</i> itu kayak Git (*Version Control System*) tapi khusus buat struktur tabel database. Jadi kamu bisa
        ngerancang dan nge-<i>share</i> wujud tabel antarteman setim tanpa perlu oper-operan *file* SQL (yang sering lupa
        di-<i>import</i>!).</p>

    <h2>Siklus Bikin Tabel Database</h2>
    <p>Gini cara bikin kerangka *table* pakai CLI Backfire:</p>
    <pre><code>php backfire make:migration create_flights_table</code></pre>
    <p>Otomatis ada *file* baru dibuatin di folder <code>database/migrations/</code> lengkap sama cap waktunya. Tinggal
        buka, terus atur mau ada kolom apa aja di fungsi penyetting <code>Schema::create</code>.</p>

    <h2>Kayak Gini Nih Bentuknya</h2>
    <pre><code>use LunoxHoshizaki\Database\Schema\Schema;
use LunoxHoshizaki\Database\Schema\Blueprint;

public function up()
{
    Schema::create('flights', function (Blueprint $table) {
        $table->id(); // Bikin ID Primary Key otomatis (INT AUTO_INCREMENT)
        $table->string('name', 100); // Teks maksimal 100 huruf (VARCHAR)
        $table->boolean('is_active'); // Bener atau salah (TINYINT)
        $table->timestamps(); // Kasih info kapan dibuat & kapan diedit
        $table->softDeletes(); // Fitur hapus pura-pura (deleted_at)
    });
}</code></pre>

    <h2>Eksekusi Migrasinya!</h2>
    <p>Kalo desain tabelnya udah beres, suruh si Backfire ngerubah rengrengannya jadi wujud fisik tabel SQL dengan *command*
        ini:</p>
    <pre><code>php backfire migrate</code></pre>

    <h2>Database Seeding (Suntik Data Massal)</h2>
    <p>Pas lagi *coding*, kita kan pasti butuh banyak "dummy data" (data palsu) buat ngetes *layout*, kan? Daripada ngisi
        manual lewat phpMyAdmin, biarin <i>Seeders</i> yang kerja kasar:</p>
    <pre><code>// 1. Suruh Backfire buatin kerangka Seedernya
php backfire make:seeder UserSeeder

// 2. Cobain tulis *logic* datanya di `app/database/seeders/UserSeeder.php`

// 3. Jebreet! Suntik semua datanya!
php backfire db:seed</code></pre>

<?php elseif ($activeLine === 'orm'): ?>
    <h1>ORM Relationships (Silsilah Antartabel)</h1>
    <p>Model di Lunox Backfire itu pinter, dia ngedukung relasi otomatis antar tabel yang saling nyambung. Jadi kamu nggak
        usah lagi repot ngetik-ngetik SQL <code>LEFT JOIN</code> segala macem (misalnya narik data 1 User beserta info semua
        Artikel/Post miliknya).</p>

    <h2>Relasi Apalagi yang Didukung?</h2>
    <ul>
        <li><code>hasOne(RelatedModel::class)</code>: Cuma punya relasi ke 1 data lain aja.</li>
        <li><code>hasMany(RelatedModel::class)</code>: Ahli poligami, punya relasi ke banyak data lain.</li>
        <li><code>belongsTo(RelatedModel::class)</code>: Mengklaim bagian dari tabel lain (Ini kebalikan dari
            `hasOne`/`hasMany`).</li>
        <li><code>belongsToMany(RelatedModel::class)</code>: Hubungan super rumit banyak-ke-banyak (Butuh campur tangan
            tabel ketiga/<i>pivot</i> sebagai perantara).</li>
    </ul>

    <p><b>Contoh Cara Pakenya:</b></p>
    <pre><code>// Panggil Si Fulan dari tabel User (Misal ID-nya 1)
$user = User::find(1);

// Ambil sekalian SEMUA koleksi tulisan Si Fulan dari tabel Post
$posts = $user->hasMany(Post::class)->get();</code></pre>

<?php elseif ($activeLine === 'storage'): ?>
    <h1>File Storage (Gudang File)</h1>
    <p>Ngelola file upload dari *user* kadang bikin keringat dingin. Tenang aja, <i>class</i> <code>Storage</code> nyediain
        API simpel nan aman biar kamu bisa asik nabung *file* di *server*.</p>
    <pre><code>use LunoxHoshizaki\Storage\Storage;

// Simpen fotonya ke dalem folder `public/storage`
Storage::put('avatars/1.jpg', $fileDataBinary);

// Dapet link lengkap buat nampilin fotonya ntar
$url = Storage::url('avatars/1.jpg');

// Buang *file* kalo udah nggak kepake
Storage::delete('avatars/1.jpg');</code></pre>

<?php elseif ($activeLine === 'cli'): ?>
    <h1>Custom CLI Commands (Bikin Perintah Konsol Sendiri)</h1>
    <p>Males ngelakuin skrip *maintenance* harian gara-gara nggak bisa diklik dari *browser*? Pindahin ke *custom script* di
        konsol CLI-nya Backfire!</p>
    <pre><code>// Bikin cikal-bakal filenya dulu:
php backfire make:command SendEmails</code></pre>
    <p>Habis ketik perintah di atas, cek *folder* <code>app/Console/Commands/SendEmails.php</code>. Buka file-nya, isi
        *logic* rutinmu di dalem fungsi <code>handle()</code>, terus jalankan aja sesuka hatimu:</p>
    <pre><code>php backfire send:emails</code></pre>

<?php elseif ($activeLine === 'mail'): ?>
    <h1>Mailer System (Kirim-Kirim Email)</h1>
    <p>Di Backfire, ngirim pesan penagihan, kode OTP, atau info diskon lewat "Mailable" itu asyiknya minta ampun. Apalagi
        sekarang mesin utamanya udah di-<i>upgrade</i> pake <b>PHPMailer</b> biar lebih kebal blokir dan pastinya langsung
        ngedukung SMTP!</p>

    <h2>Konfigurasi Dulu (File .env)</h2>
    <p>Sebelum mulai kirim-kiriman, pastiin kamu udah nyeting info SMTP di dalem dompet rahasia <code>.env</code> kamu ya:
    </p>
    <pre><code>MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=isikan_username
MAIL_PASSWORD=isikan_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello@contoh.com"
MAIL_FROM_NAME="Admin Baik Hati"</code></pre>
    <p>Kalo parameter <code>MAIL_MAILER</code> diisi <code>smtp</code>, sistem bakal otomatis ngelewatin jalur protokol SMTP
        yang stabil. Kalo nggak, dia bakal pake fungsi lawas <code>mail()</code> bawaan PHP.</p>

    <h2>Kirim Pesan Mailable</h2>
    <p>Meskipun jeroannya canggih, narik pelatuk buat ngirim emailnya tetep semanis biasanya kok:</p>
    <pre><code>use LunoxHoshizaki\Mail\Mail;

// Terbang ke Kotak Masuk target dengan bawa file Class Mailable-mu
Mail::to('user@contoh.com')->send(new WelcomeEmail());</code></pre>

<?php elseif ($activeLine === 'cache'): ?>
    <h1>Cache System (Penyimpanan Kilat)</h1>
    <p>Kalo webmu nyangkut alias lemot merender karena ada tarikan kueri SQL raksasa, atau API sebelah lagi *down*, kamu
        wajib nyimpen hasil prosesnya sementara via mekanisme <i>File Cache</i>!</p>
    <pre><code>use LunoxHoshizaki\Cache\Cache;

// Nitip hasil *query* rumit ke cache dan pertahankan selama sejam (3600 detik)
Cache::put('key_rahasia_data', 'NilaiDataYangBerat', 3600);

// Lain kali, tarik aja dari cache biar ngebut (kalo kopong, keluarin 'default')
$value = Cache::get('key_rahasia_data', 'default');</code></pre>

<?php elseif ($activeLine === 'events'): ?>
    <h1>Events & Listeners (Siaran & Reseller)</h1>
    <p>Biar isi program Controllermu nggak numpuk, pisahin logika sampingan ke teknik Pemancar (*Publisher*) dan Pendengar
        (*Subscriber*). Kayak ngisi siaran radio lokal!</p>
    <pre><code>use LunoxHoshizaki\Events\Event;

// Broadcast isyarat ke semesta kalo ada user baru daftar
Event::dispatch(new UserRegistered($user));

// Di sudut lain dunia kode, daftarin siapa aja yang kepo sama info tadi 
Event::listen(UserRegistered::class, SendWelcomeEmail::class);</code></pre>

<?php elseif ($activeLine === 'security'): ?>
    <h1>Security Dasar</h1>
    <p>Biarpun ini framework simpel, Backfire udah dibekali beberapa lapis keamanan standar buat nutupin celah-celah umum
        yang biasa diincer *bot*.</p>

    <h2>Rate Limiting (Nahan Spam)</h2>
    <p>Ada middleware <code>ThrottleRequests</code> yang bisa dipake buat ngebatasin *request*. Misalnya, kalo ada IP yang
        brutal nge-*request* lebih dari 60 kali semenit, bakal diblok sejenak.</p>
    <pre><code>use LunoxHoshizaki\Security\ThrottleRequests;

Router::get('/api/data', [ApiController::class, 'index'])
    ->middleware(ThrottleRequests::class);</code></pre>
    <p>Kalo kejadian, bakal dapet respon <code>429 Too Many Requests</code>.</p>

    <h2>Cross-Site Scripting (XSS)</h2>
    <p>Hati-hati waktu nampilin inputan dari *user* ke HTML. Selalu biasain pake helper <code>e()</code> bawaan ini buat
        nekan potensi injeksi *script* jahat:</p>
    <pre><code>&lt;!-- Rawan injeksi JS! --&gt;
&lt;?= $user->bio ?&gt;

&lt;!-- Aman, karakter HTML-nya di-escape --&gt;
&lt;?= e($user->bio) ?&gt;</code></pre>

    <h2>Cross-Origin Resource Sharing (CORS)</h2>
    <p>Kalo API-mu mau diakses dari beda *domain* (misal pake React / Vue), kamu perlu ngebolehin *CORS*. Tinggal tambahin
        aja <code>CorsMiddleware</code> di rute yang butuh:</p>
    <pre><code>use LunoxHoshizaki\Security\CorsMiddleware;

Router::group(['prefix' => '/api', 'middleware' => [CorsMiddleware::class]], function () {
    Router::get('/users', [ApiController::class, 'users']);
});</code></pre>

    <h2>Secure HTTP Headers</h2>
    <p>Buat nambahin *header* pelindung bawaan standar (kayak anti *Clickjacking* atau *MIME sniffing*), pasang aja
        <code>SecureHeadersMiddleware</code>:
    </p>
    <pre><code>use LunoxHoshizaki\Security\SecureHeadersMiddleware;

Router::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(SecureHeadersMiddleware::class);</code></pre>

<?php elseif ($activeLine === 'csrf'): ?>
    <h1>CSRF Protection</h1>
    <p>Buat nge-cegah serangan <b>Cross-Site Request Forgery (CSRF)</b> (serangan di mana *user* dipaksa ngeksekusi *action*
        tanpa sadar), Backfire punya perlindungan bawaan menggunakan *token*.</p>

    <h2>Gimana Cara Kerjanya?</h2>
    <p>Waktu *session* aktif, sistem bakal nge-<i>generate</i> "CSRF Token" unik. Tiap kali ada *request* ubah data yang
        pake *method* POST, PUT, atau DELETE, token ini wajib disertakan. Kalo nggak ada atau udan basi, proses bakal distop
        pake pesan <code>419 Page Expired</code>.</p>

    <h2>Masukin Token di Template HTML</h2>
    <p>Pake fungsi *helper* <code>csrf_field()</code> buat otomatis nyetak input tersembunyi yang isinya token tersebut di
        dalam <i>form</i>:</p>
    <pre><code>&lt;form action="/posts/simpan" method="POST"&gt;
    &lt;!-- Wajib ditaruh di dalam form! --&gt;
    &lt;?= csrf_field() ?&gt;

    &lt;label&gt;Judul Tulisan:&lt;/label&gt;
    &lt;input type="text" name="judul"&gt;

    &lt;button type="submit"&gt;Posting&lt;/button&gt;
&lt;/form&gt;</code></pre>

    <h2>Kalo Lewat AJAX (Contoh pake Axios)</h2>
    <p>Kalo aplikasimu pake AJAX, kamu tetep harus ngirim token CSRF-nya ke *server*. Cara paling simpel, simpen token di
        tag <code>&lt;meta&gt;</code> HTML:</p>
    <pre><code>&lt;meta name="csrf-token" content="&lt;?= $_SESSION['csrf_token'] ?? '' ?&gt;"&gt;</code></pre>
    <p>Baru ntar ditarik pake JavaScript dan dipasang di *Header* <code>X-CSRF-TOKEN</code> tiap nerbangin *request*:</p>
    <pre><code>// Contoh setup default di Axios
let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

axios.defaults.headers.common['X-CSRF-TOKEN'] = token;</code></pre>

<?php elseif ($activeLine === 'request-response'): ?>
    <h1>Request & Response (Beri dan Terima)</h1>
    <p>Ngoding Web itu ibarat interaksi ngobrol. Pihak pengunjung minta tolong (**Request**), server ngejawab sama respon
        balik (**Response**).</p>

    <h2>Nangkep Permohonan (Data Masuk)</h2>
    <p>Kelas sakti <code>LunoxHoshizaki\Http\Request</code> ngerangkum *file*, teks, sama info URL dari <i>user</i> biar
        gampang lu pretelin (entah dia datengnya dari metode GET bawaan link, atapun input balikan dari *form* POST).</p>
    <pre><code>public function store(Request $request)
{
    // Ambil data namanya (Ntar auto-nungguin input GET/POST sekalian)
    $name = $request->input('name');

    // Daripada panik datanya ngaco, setting aja alternatif *default*-nya disini (misal '18')
    $age = $request->input('age', 18);

    // Tamak pengen semuanya direbut jadi satu *Array* bundel?
    $data = $request->all();
}</code></pre>

    <h2>Ngeracik Paketan Balasan Utuh (Respon Kustom)</h2>
    <p>Emang sih Backfire pinter nerjemahin kembalikan `echo/return string` biasa jadi halaman jadi, kadang kan kita pengen
        ngontrol detail format isinya (kayak mau *render* balikan mentah format JSON biar di-PDKT in sama API, lengkap sama
        <i>Title Headers</i> aneh-aneh).
    </p>
    <p>Di sinilah kelas <code>LunoxHoshizaki\Http\Response</code> beraksi unjuk gigi:</p>
    <pre><code>use LunoxHoshizaki\Http\Response;

public function getJsonInfo()
{
    $response = new Response();
    $response->setContent(json_encode([
        'status' => 'success', 
        'version' => '1.11.1'
    ]));

    // Bumbuin racikan tanggapanmu dengan Header JSON dan stempel kelulusan (201 Created)
    $response->setHeader('Content-Type', 'application/json');
    $response->setStatusCode(201);

    return $response;
}</code></pre>

<?php elseif ($activeLine === 'errors'): ?>
    <h1>Error Handling (Ngurusin Kalo Aplikasi Ngambek)</h1>
    <p>Yang namanya *coding*, wajar kalo ada *error*. Tapi pas aplikasimu di-*deploy* (*Production*), pastinya kita nggak
        mau *error traceback* yang isinya kode sensitif itu dilongok sama *user* atau orang iseng.</p>

    <h2>Tampilan Error Otomatis</h2>
    <p>Kalo ada *fatal error* atau *exception* yang nggak tertangani, sistem otomatis ngubah tampilannya jadi halaman
        *error* simpel yang letaknya ada di <code>resources/views/errors/error.php</code>.</p>
    <p>Beberapa *error* umum yang langsung ditangkep sama Backfire:</p>
    <ul>
        <li><b>404 Not Found</b>: Kalo *user* ngetik URL ngasal, atau kamu nyari data ke *database* pake fungsi `find()`
            yang kebetulan kosong.</li>
        <li><b>500 Internal Error</b>: Pas ada kode PHP yang nggak jalan di server.</li>
        <li><b>419 Page Expired</b>: Waktu token CSRF-nya lupa dipasang di form atau emang udah *expired*.</li>
        <li><b>429 Too Many Requests</b>: Pas *Rate Limiter* nge-*detect* ada IP yang keseringan nge-*request*.</li>
    </ul>
    <p>*File template error* ini sengaja ditaruh di luar *core* biar kamu gampang ganti warnanya atau teks *error*-nya di
        `resources/views/errors/error.php` sesuai <i>style</i> web-mu nanti.</p>

<?php elseif ($activeLine === 'env'): ?>
    <h1>Environment Configuration (.env)</h1>
    <p>Menulis teks info rahasia (*Database Password*, *API Keys*, email *credentials*) bertelanjang dada di kode sumber
        utama itu mengundang malaikat pencabut nyawa kalo kesebar publik di GitHub atau dicopet dari FTP server!</p>

    <h2>Ngumpetin Teks Sakral (.env File)</h2>
    <p>Makanya Lunox punya pembaca <i>environment variables</i> tipe <code>.env</code>. Jadikan file gaib ini sebagai dompet
        utama hartamu ditaruh paling terluar di sarang aplikasimu.</p>
    <pre><code>APP_NAME="Lunox Backfire"
APP_ENV=local
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lunox_lite
DB_USERNAME=root
DB_PASSWORD=secret</code></pre>
    <p>Tentu aja file harta karun <code>.env</code> ini otomatis udah ditebas dari ikutan *Commit* GitHub gara-gara ke-blok
        sama <code>.gitignore</code> sakti.</p>

    <h2>Gimana Cara Nyelem Ngambil Datanya?</h2>
    <p>Seketika framework hidup, si file dompet dikerok isinya dilebur masuk *super array*. Tarik aja datanya dari ujung
        manapun file mu (contoh di *Controllers*) pake array bawaan sesepuh <code>$_ENV</code> atau manggil kakek
        <code>getenv()</code>:
    </p>
    <pre><code>// Menarik password dari balok brankas (Pakai String kosong '' jika ngaco datanya)
$dbPass = $_ENV['DB_PASSWORD'] ?? '';

if ($_ENV['APP_ENV'] === 'production') {
    // Gas ngebut jalankan script serius yang galak (Bukan mode main-main!)
}</code></pre>

<?php elseif ($activeLine === 'artisan'): ?>
    <h1>Backfire Console (Sahabat Terminalmu)</h1>
    <p>Berkenalanlah sama <code>backfire</code> CLI, aplikasi utilitas sakti yang nongkrong anteng di depan garasi folder
        aplikasimu, siap nge-bantu tugas kasar ketik-ngetik <i>file</i> bawaan.</p>

    <h2>Ngebabu Apa Aja?</h2>
    <p>Coba sapa *command launcher*-nya sama baris ini di jendela terminalmu:</p>
    <pre><code>php backfire</code></pre>

    <p>Nah ini dia kompilasi mantra-mantra CLI Backfire buat ngeringkas kerja keras mingguanmu jadi sekelebat kejapan mata
        (Alias sulap <i>Generator File</i>):</p>
    <ul>
        <li><code>php backfire make:controller AuthController</code> &rarr; Bim salabim merakit controller mentah</li>
        <li><code>php backfire make:model Product</code> &rarr; Menyatukan kelas tabel <code>products</code></li>
        <li><code>php backfire make:middleware VerifyToken</code> &rarr; Menyewa satpam pos ronda buat blokade *request*
        </li>
        <li><code>php backfire make:migration add_users_table</code> &rarr; Mencetak kertas biru denah fondasi MySQL baru
        </li>
        <li><code>php backfire migrate</code> &rarr; Menyorong kertas desain tadi disulap jadi *Table beneran* via koneksi
            database!</li>
        <li><code>php backfire db:seed</code> &rarr; Menguyur banjir data masal mainan ke seisi database seketika</li>
        <li><code>php backfire key:generate</code> &rarr; Merandom ulang kunci APP_KEY se-<i>secure</i> mungkin</li>
        <li><code>php backfire cache:clear</code> &rarr; Mengguyur sampah <i>cache</i> agar beban tersendat lepas</li>
        <li><code>php backfire serve</code> &rarr; Menghidupkan lokomotif PHP khusus menyala tanpa perlu setup rempong di
            laptopmu</li>
    </ul>

<?php endif; ?>

<?php View::endsection(); ?>
# Lunox Backfire - Panduan Lengkap

## Daftar Isi

1. [Installation & Architecture](#installation--architecture)
2. [Routing (Alur URL)](#routing-alur-url)
3. [Middleware](#middleware)
4. [Controllers](#controllers)
5. [Views (Halaman Tampilan)](#views-halaman-tampilan)
6. [Database & Models](#database--models)
7. [Validasi](#validasi)
8. [Autentikasi](#autentikasi)
9. [Global Helpers](#global-helpers)
10. [Redirect (v2.0)](#redirect-v20)
11. [Database Migrations & Seeding](#database-migrations--seeding)
12. [Sistem Hubungan ORM](#sistem-hubungan-orm)
13. [Database Transactions (v2.0)](#database-transactions-v20)
14. [File Storage](#file-storage)
15. [Pembuatan Perintah Terminal Kustom](#pembuatan-perintah-terminal-kustom)
16. [Mailer System](#mailer-system)
17. [Logging System (v2.0)](#logging-system-v20)
18. [Sistem Cache](#sistem-cache)
19. [Event Observers](#event-observers)
20. [Feature Security & DDOS](#feature-security--ddos)
21. [Proteksi CSRF](#proteksi-csrf)
22. [Hashing (v2.0)](#hashing-v20)
23. [String Parsing `Str` (v2.0)](#string-parsing-str-v20)
24. [Object Collections (v2.0)](#object-collections-v20)
25. [Representasi Request & Response](#representasi-request--response)
26. [Sistem Error Handler](#sistem-error-handler)
27. [Environment Base (.env)](#environment-base-env)
28. [Interaksi Terminal Manajemen Konsol](#interaksi-terminal-manajemen-konsol)

---

## Installation & Architecture

Selamat datang di Lunox Backfire. Ini adalah framework PHP yang didesain agar ringan dan cepat, namun tetap memberikan alat dan struktur yang memudahkan pengembangan aplikasi web.

### Cara Instal Framework

Untuk memulai, ikuti langkah-langkah instalasi awal dari repositori GitHub **lunoxhoshizaki-lite** berikut:

**1. Clone Repository**
Jika Anda ingin nama direktori *project* berbeda, tambahkan namanya di akhir perintah `git clone`:
```php
# Format: git clone [URL_REPO] [NAMA_FOLDER_KAMU]
git clone https://github.com/lunox-61/lunoxhoshizaki-lite.git my-app
cd my-app
```

**2. Install Dependencies**
Jalankan perintah Composer untuk mengunduh seluruh pustaka pendukung:
```php
composer install
```

**3. Dump Autoload**
Untuk memastikan semua *class* PHP dapat dimuat dengan baik, jalankan:
```php
composer dump-autoload -o
```

**4. Konfigurasi Environment**
Salin file konfigurasi contoh ke `.env` agar sistem dapat membaca pengaturannya:
```php
cp .env.example .env
```

### Syarat & Kebutuhan (Requirements)

Pastikan sistem Anda sudah memenuhi persyaratan berikut:
- PHP >= 8.1
- Ekstensi PDO (PHP Data Objects)
- Ekstensi OpenSSL
- Database SQL (MySQL / SQLite / MariaDB)

### Struktur Folder Inti

Berikut adalah komponen-komponen utama dalam struktur aplikasi:
- **`app/`** : Inti aplikasi Anda, memuat Models, Controllers, dan Events.
- **`database/`** : Direktori untuk file Migrations dan Seeders.
- **`public/`** : Pintu masuk aplikasi (`index.php`) dan tempat penyimpanan statik (CSS, JS, Images).
- **`resources/views/`** : File-file sistem templat tampilan (HTML/UI).
- **`routes/`** : Tempat mendaftarkan rute aplikasi (`web.php` atau `api.php`).
- **`src/`** : Kode pondasi dari framework itu sendiri.

### Menjalankan Development Server

Anda dapat menjalankan server pengembangan internal bawaan PHP melalui perintah terminal ini:
```php
php backfire serve
```
Aplikasi Anda dapat langsung diakses di `http://localhost:8000`.

---

## Routing (Alur URL)

Routing mendefinisikan tautan (URL) yang diizinkan untuk diakses pada aplikasi beserta proses yang akan dijalankan ketika tautan tersebut dituju.

### Basic Web Routing
Semua URL utama web disimpan di file `routes/web.php`. Berikut adalah contoh sederhana pengembalian teks sederhana:
```php
use LunoxHoshizaki\Routing\Router;

Router::get('/halo', function () {
    return 'Halo Dunia! Welcome!';
});
```

### Routing dengan Parameter
Untuk membuat URL dinamis yang menangkap data variabel, Anda bisa mendefinisikan parameter di antara kurung kurawal `{ }`:
```php
Router::get('/user/{id}', function ($request, $id) {
    return 'Melihat Profil Pengguna dengan ID: ' . $id;
});
```

### HTTP Methods (POST, PUT, DELETE)
Untuk menerima *request* manipulasi data dari form atau API, tersedia pendaftaran route untuk metode HTTP lainnya:
```php
Router::post('/user/simpan', [UserController::class, 'store']);
Router::put('/user/{id}/update', [UserController::class, 'update']);
Router::delete('/user/{id}/hapus', [UserController::class, 'destroy']);
```

### Route Grouping & Middleware
Route dapat dikelompokkan bersama untuk berbagi konfigurasi perlindungan *middleware* atau *prefix* tanpa duplikasi kode:
```php
Router::prefix('/admin')
    ->middleware([AuthMiddleware::class])
    ->group(function () {
        Router::get('/dashboard', [AdminController::class, 'index']); // Jadinya: /admin/dashboard
        Router::get('/setting', [AdminController::class, 'setting']); // Jadinya: /admin/setting
});
```

### Named Routes (v2.0)
Anda bisa menetapkan alias `->name()` pada suatu rute sehingga dapat lebih efisien ketika dipanggil fungsinya menggunakan *helper* `route()`:
```php
Router::get('/auth/login-karyawan-baru', [AuthController::class, 'login'])->name('login');

// Menghasilkan URL relatif:
echo route('login'); // Output: /auth/login-karyawan-baru
```

---

## Middleware

*Middleware* berfungsi sebagai filter yang memproses setiap *request* yang masuk sebelum mencapai logika aplikasi inti. Contohnya, middleware autentikasi akan memeriksa status login pengguna.

### Membuat Middleware Baru
Kelas middleware dapat digenerate secara otomatis menggunakan perintah CLI:
```php
php backfire make:middleware CheckAge
```
File ini akan dibuat pada `app/Http/Middleware` atau `app/Middleware`.

Di dalam middleware, Anda dapat mendefinisikan logika kondisional pada metode `handle`:
```php
namespace App\Middleware;

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
}
```

### Mendaftarkan Middleware ke Route
Terapkan kelas Middleware tersebut ke *route* di dalam `routes/web.php` atau `routes/api.php`.
```php
use App\Middleware\CheckAge;

// Spesifik pada satu rute
Router::get('/khusus-dewasa', function () {
    return 'Selamat datang!';
})->middleware(CheckAge::class);

// Diimplementasikan pada grup rute tertentu
Router::prefix('/admin')->middleware([CheckAge::class])->group(function () {
    Router::get('/dashboard', [AdminController::class, 'index']);
});
```

---

## Controllers

**Controllers** mengelompokkan logika penanganan permintaan HTTP yang terkait ke dalam sebuah kelas. Pendekatan ini menjaga alur logika agar terpisah dari pengaturan file `web.php`.

### Membuat Controller
Gunakan perintah artisan *make*:
```php
php backfire make:controller UserController
```
Ini membuat file persiapan di `app/Controllers/UserController.php`.

### Struktur Dasar Controller
Controller menerima iterasi dari object Request yang berisi data masukan dan merangkai balasan berupa Tampilan *View* (HTML) atau Respon JSON.
```php
namespace App\Controllers;

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
}
```

### Menyambungkan Controller ke Route
Metode pada Controller dipanggil dengan pendekatan berbasis *Array*:
```php
Router::get('/user/{id}', [UserController::class, 'show']);
```

---

## Views (Halaman Tampilan)

*Views* berguna untuk merender antarmuka pengguna, memisahkan lapisan presentasi (HTML) dari logika bisnis (Controller/Models). File Views dipusatkan pada direktori `resources/views`.

### Merender Views
Tampilan dimuat melalui class bawaan `View::make`, ekstensi file `.php` tidak perlu disertakan.
```php
// Akan mereferensikan file resources/views/greeting.php
return View::make('greeting', ['nama' => 'Budi']);
```
Variabel sisipan dapat dimanfaatkan di dalam `greeting.php` (Catatan: gunakan helper `e()` untuk mitigasi XSS):
```php
<h1>Halo, <?= e($nama) ?>!</h1>
```

### Layouts (Templating System)
Fitur *Template Inheritance* memudahkan Anda mereusibilitas bagian umum antarmuka (misal *Header* dan *Navigasi*).

**1. Pembuatan Master Template (Di `resources/views/layouts/app.php`):**
```php
<html>
<head><title>Aplikasi Default</title></head>
<body>
    <!-- Titik pemanggilan area konten dinamis -->
    <?= $this->yieldContent('content') ?>
</body>
</html>
```

**2. Mengimplementasikan Layout di View Utama:**
```php
<?php View::extends('layouts.app'); ?>

<?php View::section('content'); ?>
    <h1>Isi konten eksklusif halaman ini.</h1>
<?php View::endsection(); ?>
```

---

## Database & Models

Lunox Backfire menyertakan pola desain dasar perangkat lunak *ActiveRecord* yang menyederhanakan cara Anda mengoperasikan baris basis data layaknya sebuah objek PHP biasa.

### Konfigurasi Database
Detail koneksi disesuaikan ke dalam file *environment* `.env` konfigurasi Anda:
```php
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_name
DB_USERNAME=root
DB_PASSWORD=password
```

### Pembuatan Model
Perwakilan skema entitas data didukung melalui kelas **Model**. Gunakan instruksi CLI ini:
```php
php backfire make:model Product
```
Konvensi Backfire mendeteksi bentuk jamak dari nama Model sebagai target *Tabel database*-nya (misal: `Product` memetakan langsung ke struktur relasi tabel `products`).

### Operasi Dasar CRUD (Nambah, Baca, Edit, Hapus)
Siklus pengolahan data standar dapat dilakukan tanpa pengetikan SQL manual:
```php
// CREATE: Menyisipkan Data Baru
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
$produkHapus->delete();
```

### Query Builder Lanjutan
Objek Query Model menyokong *sorting*, *limit*, maupun *Paginasi* langsung untuk pengolahan komprehensif:
```php
// Kueri yang memuat fungsi-fungsi kondisional lebih rinci (whereIn, whereNull, leftJoin)
$terlaris = Product::query()
    ->leftJoin('categories', 'products.cat_id', '=', 'categories.id')
    ->whereIn('tags', ['book', 'pen', 'paper'])
    ->whereNull('deleted_at')
    ->orderBy('sold_count DESC')
    ->limit(10)
    ->get();

// Integrasi pemecahan kumpulan Array ke UI Halaman terputus (Paginasi)
$semuaProduk = Product::paginate(15);
```

Fungsi relasi pemecahan Paginasi itu diformulasikan langsung sebagai navigasi HTML (Bootstrap compatible):
```php
<!-- Mencetak markup blok navigasi (<< 1 2 3 >>) otomatis -->
<?= $semuaProduk->links() ?>
```

---

## Validasi

Prosedur memvalidasi permintaan yang dikirimkan oleh pengguna sangat dianjurkan untuk mendeteksi anomali pada masukan *form request*.

### Validasi Objek Controller (v2.0)
Atribut persyaratan diimplementasikan secara deskriptif untuk *Request object* meliputi verifikasi eksistensi isian, batasan String hingga keunikan format Tipe data:
```php
public function store(Request $request)
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
}
```

### Informasi Respon Error di View
Jika pengguna melakukan kesalahan saat submisi form, *Error bag log* dicatat sekilas pada memori sesi. Helper bawaan `errors()` dan `old()` disediakan untuk interaksi UI yang layak (misal tidak mengosongkan nilai yang pernah didikte pengguna):
```php
<form action="/simpan" method="POST">
    <!-- Token keamanan minimal pelindung Form -->
    <?= csrf_field() ?>

    <label>Judul:</label>
    <input type="text" name="title" value="<?= e(old('title')) ?>">

    <!-- Pemeriksaan logistik error 'title' apabila validasi dikembalikan sistem -->
    <?php if ($error = errors('title')): ?>
        <div style="color:red;"><?= $error ?></div>
    <?php endif; ?>

    <button type="submit">Kirim Data</button>
</form>
```

### Ekstraksi Validasi Form Khusus
Untuk menyingkirkan logika berat di *Controller*, tersedia berkas kelas abstrak *FormRequest* dengan memanggil `php backfire make:request StoreDataRequest`.

---

## Autentikasi

Sistem otentikasi konvensional dapat dengan mudah dikoordinir berbekal dukungan *Facade* `Auth` untuk keperluan fungsional memfasilitasi status Login pengguna.

### Registrasi & Hashing Objek
Dalam mendaftarkan akun baru, konversi Password standar diubah menggunakan `Hash::make()` berbasis ekstensi inti PHP (*Lihat sesi dokumentasi Hashing*):
```php
use LunoxHoshizaki\Security\Hash;

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
}
```

### Fase Logaritma Otentikasi
Verifikasi kata sandi secara implisit terkelola tanpa *query* kompleks melalui `Auth::attempt`, yang terintegrasi secara teknis terhadap format tabel `User` bawaan:
```php
use LunoxHoshizaki\Auth\Auth;

if (Auth::attempt(['email' => $email, 'password' => $password])) {
    return redirect('/dashboard');
} else {
    return redirect()->back()->withErrors(['message' => 'Email atau password salah.']);
}
```

### Pemanggilan Profil Login Singgah
Entitas `Auth` membawakan akses membedah identitas pengguna di manapun *scoping* aplikasi berjalan:
```php
// Ekstrak referensi instans User yang login di saat yang sama
$user = Auth::user();
echo $user->email;

// Spesifik pencarian Integer penomoran ID
$id = Auth::id();

// Operasional pengecekan sederhana *boolean-check*
if (Auth::check()) {
    // Tervalidasi
}
```

### Membatasi Penetrasi Halaman Melalui Middleware
Kombinasi rute privat (Contoh dasbor internal) dicegah akses tidak login-nya melalui injeksi *Class* `AuthMiddleware`:
```php
Router::get('/profile', [ProfileController::class, 'show'])
->middleware(LunoxHoshizaki\Security\AuthMiddleware::class);
```

---

## Global Helpers

Berbagai fungsi bantuan (*helpers*) global mendistribusikan utilitas pengerjaan dasar di semua skope direktori untuk efisiensi akses logika tanpa harus selalu memanggil Namespace objek lengkap.

- `old('field_name')` : Mengembalikan riwayat masukan awal formulir tatkala fase proses input ditolak.
- `errors('field_name')` : Mendapatkan rangkaian nilai pesan kesalahan masukan validasi.
- `csrf_field()` : Men-generate HTML format elemen untuk kebutuhan intervensi serangan keamanan *form request*.
- `e($string)` : Mengonversi objek parameter ke *string HTML escape* yang aman diperlihatkan pada sistem templating.
- `asset('css/style.css')` : Konversi path dokumen absolut menuju sumber File Media pada jalur `public/`.
- `config('app.name')` : Pengambilan objek properti statis spesifik milik arsip pusat konfigurasi.
- `route('nama_rutemu')` : Menampilkan alamat URI berdasar atas penamaan rute.
- `now()` : Fungsi ekstraksi tanggal dan waktu lokal dari kapabelitas PHP (`Y-m-d H:i:s`).
- `dd($vars), dump($vars)` : *Utility tracer* pembantu saat *development* untuk memvisualisasi data koleksional.
- `collect($array)` : Mewakilkan *array format* awam ke kelas Collection yang memiliki ragam bantuan ekstensi pengkondisian iterasi.

---

## Redirect (v2.0)

Menavigasikan kemudi lalu lintas pengunjung bisa secara halus diperankan lewat asisten `Redirect` Helper.

### Metode Penggunaan:
```php
// Navigasi Standar URL Relatif
return redirect('/home');

// Menerjunkan pengunjung kembali menuju rute sebelumnya (Referer Tracker)
return redirect()->back();

// Memberikan paket sesi pesan Error Form dengan dukungan *input recovery*
return redirect()->back()
    ->withErrors(['username' => 'Format masukan telah digunakan'])
    ->withInput();

// Memberikan tanda *Flash Session* ke UI selanjutnya 
return redirect('/dashboard')->with('success_alert', 'Proses sinkronisasi data berhasil disahkan!');
```

---

## Database Migrations & Seeding

*Migrations* memberikan lapisan version control interaktif yang bertugas merancang modifikasi cetakan basis data berkesinambungan bagi semua tim *engineers* tanpa mengeksekusi operasi SQL tertulis manual.

### Siklus Cetakan Migrasi
Deklarasi struktur rintisan tabel dibuat memakai konsole CLI:
```php
php backfire make:migration create_flights_table
```
Hal ini memfasilitasi *draft* dengan tanggal identifikasi yang letaknya di `database/migrations/`. Silakan merekonstruksi skema kolom lewat properti abstrak `Schema::create`.

### Contoh Referensi Konstruksi Schema (v2.0)
```php
use LunoxHoshizaki\Database\Schema\Schema;
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
}
```

### Instruksi Mengeksekusi Migrasi Sistem
Perintahkan *engine* CLI untuk mengubah kerangka struktur logikal file Migration tersebut ke operasi valid Database Engine:
```php
php backfire migrate
```

### Database Seeding (Pengisian Data Sintetis)
Kehadiran Seeder meminimalisir intervensi input basis data primitif pada awal pengembangan melalui sistem penyuntikan *Mockup array*.
```php
// 1. Deklarator Kerangka *Class*
php backfire make:seeder UserSeeder

// 2. Tulis konstruksi Dummy record di `app/database/seeders/UserSeeder.php`

// 3. Modul injeksi keseluruhan baris parameter tersebut
php backfire db:seed
```

---

## Sistem Hubungan ORM

Implementasi model entitas *ActiveRecord* mengelaborasi pola relasi tabel database secara dinamis sehingga menyederhanakan penarikan informasi `LEFT JOIN` terstruktur otomatis untuk skema antar-tabel.

### Model Relasi Didukung
- `hasOne(RelatedModel::class)`: Properti korelasi relasi tunggal eksklusif.
- `hasMany(RelatedModel::class)`: Entitas referensi dengan basis distribusi majemuk (Sistem List Data Induk-Anak).
- `belongsTo(RelatedModel::class)`: Mempresentasikan kepemilikan invers tunggal dari definisi relasi lain.
- `belongsToMany(RelatedModel::class)`: Hubungan skala besar majemuk (*Many-to-Many*), lazim mengaplikasikan integrasi *pivot table*.

**Referensi Akses Data:**
```php
// Identifikasi objek instance induk 
$user = User::find(1);

// Kueri relasional mengaitkan properti fungsional array dari relasi terdaftar
$posts = $user->hasMany(Post::class)->get();
```

---

## Database Transactions (v2.0)

Sistem transaksi basis data memastikan keselamatan tahapan eksekusi logika mutasi berurutan agar jika ada parameter error tidak ada penyimpanan parsial di struktur database inti.

### Transaction Closure
```php
use LunoxHoshizaki\Database\DB;

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
```

---

## File Storage

Intervensi sistem integrasi arsip dokumen dapat terbantu menggunakan fitur pendistribusian lokal sederhana dari kelas `Storage`.
```php
use LunoxHoshizaki\Storage\Storage;

// Penyimpanan statis fisik berbasis direktori `public/`
Storage::put('avatars/1.jpg', $fileDataBinary);

// Pembuatan string referensi penautan web (URL Asset) untuk keperluan render halaman
$url = Storage::url('avatars/1.jpg');

// Fungsi pembersihan arsip secara presisi
Storage::delete('avatars/1.jpg');
```

---

## Pembuatan Perintah Terminal Kustom

Rincian operasional rutin layaknya utilitas cron job latar belakang (*scripts maintenance*) dapat disusun pada unit CLI Command tertulis internal aplikasi.
```php
// Perintah generasi Kelas
php backfire make:command SendEmails
```
File basis perintah akan tercipta di `app/Console/Commands/SendEmails.php` untuk menampung argumen proses kompensasi data massal. Pemicu utama dijalankan berdasar inisiasi nama spesifik *command signiture*:
```php
php backfire send:emails
```

---

## Mailer System

Sistem transmisi surat terpaket *Mailable* difungsikan dalam menyajikan pesan faktur email konfirmasi dengan penataan layout tertata rapi memanfaatkan SMTP native dari library pendukung **PHPMailer**.

### Konfigurasi Lingkungan Surat
Kredensial infrastruktur pesan elektronik (*SMTP Mail*) berada pada area modifikasi di file `Environment` sentral:
```php
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=isikan_username
MAIL_PASSWORD=isikan_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello@contoh.com"
MAIL_FROM_NAME="Admin Notifikasi"
```
Pengaturan format `MAIL_MAILER=smtp` menandakan alur kompensasi port berjamin server *Third-Party*, dan meniadakan penggunaan `mail()` murni di server aplikasi.

### Protokol Mengirim Email Format Mailable
```php
use LunoxHoshizaki\Mail\Mail;

// Integrasi pemaketan file `class Mailable` kepada entitas alamat yang dirujuk
Mail::to('user@contoh.com')->send(new WelcomeEmail());
```

---

## Logging System (v2.0)

Pencatatan rekap sirkulasi log vital program sistem tersimpan otomatis secara aman layaknya penanda rotasi harian dengan susunan `storage/logs/lunox-YYYY-MM-DD.log`.

### Sintaksis Peringatan Log
```php
use LunoxHoshizaki\Log\Log;

// Log informasi normal dan sukses
Log::info('Aktivitas Login tercatat di sesi saat ini.', ['user_id' => 5]);

// Indikasi kesalahan interaksi non-fatal fungsional
Log::error('API Transaksi gagal membalas tiket.', ['respon_code' => 500]);

// Respon Kritis Kegagalan Fundamental
Log::critical('Interupsi Koneksi Database Engine!');
```

---

## Sistem Cache

Penyimpanan entitas berelasi besar dalam file statis memperingan sistem pembacaan kueri terdistribusi *Database server* selama skala rentang penahanan rotasi tertentu yang diatur.

**SECURITY NOTE (V2.0):** Format skema integrasi *engine* pemanggil file Cache menggunakan standar JSON *encoder* yang terhindar dari pemanfaatan celah injeksi via modul pembawa `unserialize()`. Jalankan `php backfire cache:clear` pasca revisi versi.

```php
use LunoxHoshizaki\Cache\Cache;

// Memuat penahanan nilai informasi khusus sampai batasan kalkulasi (waktu detik)
Cache::put('key_rahasia_data', 'Sistem Konfigurasi Beban', 3600);

// Identifikasi nilai objek dan penyertaan balasan alternatif (*Fallback parameter*)
$value = Cache::get('key_rahasia_data', 'default');
```

---

## Event Observers

Isolasi logika bisnis ekstra (misal mengirim format faktur) dengan membagi instansi pemrograman pemancar (*Publisher Event Actions*) dan penerima intervensinya (*Listener*).
```php
use LunoxHoshizaki\Events\Event;

// Menciptakan pancaran pemicu event sistem
Event::dispatch(new UserRegistered($user));

// Eksekutor pendaftar fungsi logis apabila interaksi rute spesifik terpancarkan 
Event::listen(UserRegistered::class, SendWelcomeEmail::class);
```

---

## Feature Security & DDOS

Struktur model proteksi ISO dan mitigasi standar OWASP dapat diperbantukan pada level arsitektur middleware sistem aplikasi.

### Rate Limiting
Lapisan *throttle* ditanamkan sebagai modul pengawasan jumlah lalu-lintas permintaan pada unit alamat IP tertentu; hal yang lazim digunakan demi proteksi DDoS (*Brute-force limit*).
```php
use LunoxHoshizaki\Security\ThrottleRequests;

Router::get('/api/data', [ApiController::class, 'index'])
    ->middleware(ThrottleRequests::class);
```

### Mitigasi XSS Injeksi
Konversi rendering pengolahan karakter dari parameter eksternal dimuat melewati mekanisme mitigasi String dengan fungsi `e()` untuk meminimalisasi celah manipulasi skrip (*Cross-Site Scripting*).
```php
<!-- Implementasi pelolosan yang sah format HTML -->
<?= e($user->bio) ?>
```

### Pengaturan Identitas Skema CORS
Rilis rute pada sistem luar beda lingkup Domain (Aplikasi UI *SPA* React dsb) dikelompokan di dalam fungsi pengawasan `CorsMiddleware`.
```php
use LunoxHoshizaki\Security\CorsMiddleware;

Router::prefix('/api')->middleware([CorsMiddleware::class])->group(function () {
    Router::get('/users', [ApiController::class, 'users']);
});
```

### Header Properti HTTP Lanjutan (v2.0)
Protektif skema MIME Header (*CSP/ClickJacking*) secara mutlak diformat melalui rincian arsitektural *Class SecureHeadersMiddleware* yang dapat meredam manipulasi modifikasi akses browser tidak terverifikasi.
```php
use LunoxHoshizaki\Security\SecureHeadersMiddleware;

Router::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(SecureHeadersMiddleware::class);
```

---

## Proteksi CSRF

Pengamanan formulir mencegah serangan kerentanan *Cross-Site Request Forgery (CSRF)* yang memungkinkan parameter rute di luar batas otorisasi pengguna ditransmisikan dan dimanipulasi.

### Mekanisme Alur Verifikasi Form
Identifikasi log sesi secara tertutup mencatat kepemilikan string "Token CSRF". Validasi operasional *HTTP Method* modifikasi Data seperti baris PUT, POST dan DELETE diwajibkan menyertakan pengajuan Token tervalidasi yang relevan agar tak tertolak skema Respon `419 Expired`.

### Menyiapkan Pengajuan Parameter HTML
Elemen baris rahasia di dalam struktur cetakan formular didukung *Function Render* instansi token tersebut via sisipan `csrf_field()`:
```php
<form action="/posts/simpan" method="POST">
    <?= csrf_field() ?>

    <label>Judul Tulisan:</label>
    <input type="text" name="judul">

    <button type="submit">Posting</button>
</form>
```

### Konfigurasi Interaksi AJAX
Transmisi formulir asinkron secara *Javascript API* mendukung parameter identifikasi referensi penempatan *meta-tags*.
```php
<meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?? '' ?>">
```
Header pengajuan tersebut dimanfaatkan oleh integrasi arsitektur request kustom eksternal:
```js
// Modifikasi parameter *Header Request* di klien web
let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
```

---

## Hashing (v2.0)

Kelas Hash secara cerdas mengadaptasi algoritma kriptografi tercanggih berstandar (seperti Argon2Id atau BCrypt) dalam pengolahan hash yang aman.

### Format Otentikasi Terenkripsi
```php
use LunoxHoshizaki\Security\Hash;

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
```

---

## String Parsing `Str` (v2.0)

Berbagai referensi standar manajemen kata logis diimplementasi di modul fasilitator utilitas fungsional *String Extractor* statis pada Class `Str`.
```php
use LunoxHoshizaki\Support\Str;

// Mutator properti baris kata pemformat URL
echo Str::slug('Pemformat Parameter URL'); // "pemformat-parameter-url"

// Standar pengacakan indeks acuan Identitas v4 Universial (UUID)
echo Str::uuid(); 

// Intervensi konversi tata teks program
echo Str::snake('UserControllerName'); // "user_controller_name"

// Limitator referensi kuantitas karakter awam pada suatu referensi *preview*
echo Str::limit('Pembatasan deskripsi tampilan antarmuka web sistem', 15); // "Pembatasan de..."
```

---

## Object Collections (v2.0)

Utilitas Collection yang merangkul struktur data *Array Multidimensi* mendukung sintaks interaktif dengan antarmuka yang lebih tertata jika disejajarkan oleh skema nativ php.

### Fungsi Pemrosesan Integratif Set Matrix
```php
// Konstruksi basis tabel matriks entri array terkelola
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
```
Manajemen operasional himpunan terprogram mendukung fungsi standar industri lainnya meliputi: `map, filter, reduce, chunk, merge, keyBy, dll.`

---

## Representasi Request & Response

Konfigurasi parameter web sistem diterjemah sebagai siklus dua entitas objek yakni parameter pengguna yang diminta (**Request**) dan bentuk respon (*Rendering/JSON*) antarmuka (**Response**).

### Intervensi Payload Request Data
Instansiasi abstrak pada objek `LunoxHoshizaki\Http\Request` meredam kompleksitas pembacaan skrip URI secara konsisten dari skema eksekusi operasi HTTP Form Data maupun properti parameter navigasi link GET.
    
**Bentuk Data Tipikal (v2.0 Feature)**
Fungsi tipikal pendefinisian permintaan difilter lebih awal (*Typecasting*) untuk keamanan konsistensi komparasi format basis data sistem yang ketat.
```php
public function store(Request $request)
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
}
```

### Inisiasi Format Sistem Respon Data Tervalidasi
Rincian properti objek Respon memungkinkan pengalihan spesifik terhadap Header MIME *Status code* di mana format integrasi *Content-Type* spesifik dimanipulasi layaknya arsitektural API.
```php
use LunoxHoshizaki\Http\Response;

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
}
```

---

## Sistem Error Handler

Resepsi konfigurasi rincian eksekutor kueri *Trace Exception* pada sistem *Production Environment Server* dialihkan layaknya notifikasi yang aman serta seragam dari visual pengguna akhir.

### Visual Konfigurasi Tampilan Otomatis
Cakupan kesalahan referensial eksekusi parameter fatal diarahkan secara tunggal melingkungi *template view fallback* eksternal yang eksis pada `resources/views/errors/error.php`.
- **404 Tautan Tak Tersedia**: Tangkapan atas kegagalan eksistensi sistem dari *controller finder*.
- **500 Kegagalan Logika Mesin Server**: Pemroses kode bermasalah.
- **419 Kedaluwarsa Sesi**: Referensi CSRF *Token Timeout* yang ditahan.
- **429 Resepsi *Throttle* Terlampaui**: Notifikasi pembatasan integrasi masukan jaringan akses (*Rate Limit*).

---

## Environment Base (.env)

Abstraksi manajemen rahasia pengontrol pusat kredensial dipisahkan secara hierarki parameter dari entri program operasional koding (*source controller*).

### Deklarasi Konstanta Server Global
Framework membedah konfigurasi dari komponen pembaca eksternal berformat isolasi root server `.env`.
```php
APP_NAME="Aplikasi Produksi"
APP_ENV=local
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_core
DB_USERNAME=root
DB_PASSWORD=secret
```
Parameter sensitivitas dokumen dikecualikan dari komit repo utama via penyertaan di ekstensi `.gitignore` standar *security compliance*.

### Sistem Penyaringan Variabel Properti Bawaan (v2.0)
Prosedur utilitas sistem meniadakan kemungkinan eksistensi variabel kosong jika dimuat skriptual saat nilai terdegradasi / absen tanpa peredam eror *Fallback parameter* bawaan di dalam *array* sistem.
```php
// Modifikasi penangkapan parameter sistem spesifik dari konfigurasi Environtment (*Helper*)
$secret = env('DB_PASSWORD', 'NilaiFallbackPenting');

// Menarik hierarki manajemen Array asosiatif bertingkat dari folder Konfigurasi spesifik `.dot-syntax`
$namaWeb = config('app.name', 'Framework Ajib');
```

---

## Interaksi Terminal Manajemen Konsol

Sistem intervensi manajemen komando perintah *script executable* berjenis `backfire` berdiam sebagai basis aplikasi portabel pelaksana administrasi utiliatas pengembang (*Developer Toolbar CLI*).

### Sintaksis Manajemen Skrip Terminal
Penginisialisasian utama intervensi operasi berjalan dieksekusi dengan *PHP Interpreter Engine* sebagai rintisan argumen di panel komando.
```php
php backfire
```

Berikut adalah beberapa komponen instruksi *Scaffolding builder* (*File Generator Template*) yang disertakan untuk pemrosesan lebih ringkas:
- `... make:controller DataController` -> Membuat fondasi model klasifikasi controller rute program.
- `... make:request StoreDataRequest` -> Pembangkit formulasi Custom Model Validator form input (v2.0 update).
- `... make:model LogSistem` -> Menciptakan relasional sistem pembaca kelas Tabel (*Object Relational Mapping*).
- `... make:middleware UserScope` -> Menghadirkan pengerjaan modul lapis filter pencegah intervensi akses log.
- `... make:migration add_history_table` -> Membentuk versi pemetaan kerangka tabel struktural rancang bangun MySQL Engine.
- `... migrate` -> Menjalankan proses implementasi cetakan skema mutakhir pada pangkalan server database sentral.
- `... db:seed` -> Memicu populasi sampel dummy rekam jejak masal terhadap parameter data lokal simulasi pengembangan.
- `... key:generate` -> Mensinergi ulang pembuatan angka kunci basis aplikasi demi kriptografi internal teraman.
- `... cache:clear` -> Merekonstruksi dan melegakan retensi ukuran berkas *data caching* stagnasi lama.
- `... serve` -> Menjadi lokomotif pembantu simulasi Web Development Engine secara localhost.

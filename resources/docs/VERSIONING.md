# Skema Pembaruan (Versioning Scheme) Lunox Backfire

Proyek ini menggunakan standar **Semantic Versioning (SemVer)** untuk penomoran versi. Skema pembaruan berdasarkan tiga angka, dengan format **`vMAJOR.MINOR.PATCH`** (contoh: `v1.1.0`).

## Struktur Penomoran

1. **MAJOR (Mayor)**: Angka pertama (contoh: **1**.0.0)
   - **Kapan diperbarui?** Ketika Anda membuat pembaruan besar yang mengubah arsitektur inti atau fitur yang tidak kompatibel ke belakang (*backward-incompatible*).
   - **Contoh**: Merombak sistem *routing* secara keseluruhan atau mengubah struktur *database* yang mengharuskan pengguna melakukan migrasi besar.

2. **MINOR (Minor)**: Angka kedua (contoh: 1.**1**.0)
   - **Kapan diperbarui?** Ketika Anda menambahkan fitur baru, halaman baru, atau fungsionalitas tambahan yang masih kompatibel dengan versi sebelumnya (*backward-compatible*).
   - **Contoh**: Menambahkan fitur *login*, menambahkan *middleware* baru, atau menambah integrasi UI.

3. **PATCH (Tambalan/Perbaikan)**: Angka ketiga (contoh: 1.1.**1**)
   - **Kapan diperbarui?** Ketika Anda melakukan perbaikan *bug*, perbaikan tata letak, atau optimasi kecil yang tidak menambah fitur baru.
   - **Contoh**: Memperbaiki tautan yang rusak, memperbaiki tampilan *loading*, menambal celah keamanan kecil (seperti *typo*), dll.

---

## Alur Pembaruan (*Update Flow*)

Setiap kali Anda merilis pembaruan, ikuti langkah-langkah berikut untuk mengelola versi proyek:

### 1. Ubah Versi di `.env`
Buka file `.env` (dan sesuaikan di `.env.example` untuk acuan).
```env
APP_VERSION=1.1.0
```
Ubah angka tersebut sesuai dengan tipe rilis yang Anda kerjakan (MAJOR, MINOR, atau PATCH). Karena tampilan `layouts/app.php` kita sudah membaca variabel ini, pembaruan di UI otomatis beradaptasi.

### 2. Catat Perubahan (Changelog)
Sangat direkomendasikan untuk mencatat apa saja yang berubah di file `CHANGELOG.md` setiap Anda menaikkan versi.
**Contoh Format:**

```markdown
## [1.11.1] - 2026-03-03
### Security (ISO 27001 Patches)
- **[A.14]** Bumped minimum PHP requirement to `^8.1` to mitigate EOL vulnerabilities.
- **[A.14]** Restricted detailed exception and stack trace rendering when `APP_ENV=production`.
- **[A.13]** Enforced secure session cookie parameters (`httponly`, `use_strict_mode`, and `secure` conditionally).

## [1.11.0] - 2026-03-03
### Added
- **[Phase 6]** Security & Anti-DDoS Protections.
- **[Phase 6]** Rate Limiting (`ThrottleRequests` Middleware).
- **[Phase 6]** Auto-Escaping XSS Protection (fungsi helper global `e()`).
- **[Phase 6]** CORS implementation (`CorsMiddleware`).
- **[Phase 6]** Secure HTTP Headers (`SecureHeadersMiddleware`).
- Pembaruan dokumentasi internal `/docs` bagian Security.

## [1.10.0] - 2026-03-03
### Added
- **[Phase 5]** Database ORM Relationships (`hasOne`, `hasMany`, `belongsTo`, `belongsToMany`).
- **[Phase 5]** File Storage System (`Storage` memfasilitasi manajemen file dengan local/public driver).
- **[Phase 5]** Custom CLI Commands (`make:command` serta registrasi command dinamis di script `backfire`).
- **[Phase 5]** Mailer System (kelas `Mail` dan `Mailable` untuk pengiriman email terstruktur).
- **[Phase 5]** Cache System (File-based cache caching via `Cache::put`, `Cache::get`).
- **[Phase 5]** Events & Listeners (Sistem Pub/Sub memisahkan logika aplikasi).
- Pembaruan dokumentasi internal `/docs` yang mencakup semua fitur baru.

## [1.9.0] - 2026-02-22
### Added
- **[Phase 4]** Pembangun Skema Database (`Schema` & `Blueprint`) untuk migrasi. Pembuatan file migrasi (`make:migration`) sekarang menggunakan format `Schema::create` (mirip Laravel) sepenuhnya daripada `PDO` mentah.

## [1.8.0] - 2026-02-22
### Added
- **[Phase 3]** Refaktorisasi sistem Migrasi ke format Kelas PHP ala Laravel (`up()` & `down()`).
- **[Phase 3]** Perintah CLI `php backfire make:seeder` dan `php backfire db:seed` untuk mendukung Seeding database otomatis melalui `DatabaseSeeder.php`.

## [1.7.0] - 2026-02-22
### Added
- **[Phase 2]** Advanced Query Builder with `join()`, `orderBy()`, `limit()`, and Pagination (`paginate()`).
- **[Phase 2]** Model Traits: `SoftDeletes` dan `HasTimestamps`.
- **[Phase 2]** Form Request Validation melalui `$request->validate()`.
- **[Phase 2]** Authentication Manager (`Auth::attempt()`, dll) dan `AuthMiddleware`.
- **[Phase 2]** Session Flash Manager dengan fungsi helper View global (`old()`, `errors()`, `csrf_field()`, `asset()`).
- **[Phase 2]** CLI command baru: `make:migration` dan `migrate`.

## [1.6.2] - 2026-02-22
### Fixed
- Memperbaiki View Engine (`LunoxHoshizaki\View\View::make()`) yang sebelumnya tidak mendukung *nested layouts*, menyebabkan halaman `/docs` tampil putih/kosong. Kini view mendukung pewarisan (extends) secara rekursif tak terbatas.

## [1.6.1] - 2026-02-22
### Changed
- Refactoring UI halaman `/docs` agar konsisten dengan `layouts/app.php` (Home & About).
- Menghapus Tailwind CSS dan menggunakan Bootstrap 5 serta Google Sans Flex untuk menyeragamkan desain utama aplikasi.

## [1.6.0] - 2026-02-22
### Added
- Fitur Dokumentasi internal di rute `/docs`.
- Tampilan UI dokumentasi khusus menggunakan Tailwind CSS yang responsif dan mirip dokumentasi Laravel versi 10+.
- Menambahkan `DocsController` beserta halaman informasi instalasi, routing, controller, view, dan model.

## [1.5.0] - 2026-02-22
### Added
- Penambahan CLI Tool bernama `backfire` untuk mempermudah developer scaffold project (mirip artisan).
- Perintah `php backfire make:controller`, `make:model`, `make:middleware`.
- Perintah operasi environment `php backfire key:generate`, `cache:clear`, `serve`.

## [1.4.0] - 2026-02-22
### Added
- Penambahan halaman Debug Error (ala Whoops) yang interaktif ketika `APP_DEBUG=true`.
- Menampilkan *Stack Trace* dan potongan kode (Code Snippet) spesifik di line tempat error terjadi.

## [1.3.0] - 2026-02-22
### Added
- Penambahan halaman View kustom untuk menangani Error 404, 419, dan 500 Exceptions.
- Update `Application.php` untuk merender exception menjadi tampilan error UI yang ramah pengguna.

## [1.2.0] - 2026-02-22
### Added
- Setup API menggunakan Token otentikasi.
- Middleware `ApiAuthMiddleware`.
- Pemisahan rute `routes/api.php` di-load bersama `web.php`.

## [1.1.0] - 2026-02-22
### Added
- Penambahan Google Sans Flex font dari Google Fonts.
- Ikonografi baru menggunakan Material Symbols Outlined.
- Skema update versi dinamis di tampilan UI.

### Changed
- Nama aplikasi dan author diubah ke "Lunox Backfire" dan "Lunox Hoshizaki".
```

### 3. Pengecekan Lingkungan (Deployment)
Jika pembaruan tersebut bersifat **MAJOR** atau berkaitan dengan *Database*, pastikan ada panduan *upgrade* (misal: "Jalankan skrip `database_update.sql`") agar mempermudah pembaruan di tingkat operasional.

## Tahapan Versi Khusus (Pre-release)
Kadang Anda membuat versi *alpha* atau *beta* untuk uji coba. Tambahkan *label* di akhir penomoran:
- **`v1.2.0-alpha`**: Fase percobaan internal / baru dibangun.
- **`v1.2.0-beta`**: Fase percobaan untuk mendeteksi *bug* sebelum rilis publik.
- **`v1.2.0-rc.1`** (*Release Candidate*): Versi yang sudah matang dan siap dirilis kecuali ada *bug* fatal terakhir.

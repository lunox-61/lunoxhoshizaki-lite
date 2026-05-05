# Lunox Backfire v2 Update Notes

## ⚡ v2.3.0 - API Support & JWT Foundation (2026-05-05)

Update **v2.3.0** memfokuskan pada penyiapan fondasi API yang solid dan siap produksi, mencakup sistem respons standar, JWT, dan scaffolding tooling.

### Komponen Baru:
1. **`ApiResponse`** (`src/Http/ApiResponse.php`): Factory class untuk respons JSON standar dengan envelope `{success, message, data, meta}`. Tersedia method: `success()`, `error()`, `created()`, `paginated()`, `notFound()`, `unauthorized()`, `forbidden()`, `validationError()`, `tooManyRequests()`, `noContent()`, `serverError()`.
2. **`ApiController`** (`src/Api/ApiController.php`): Base controller untuk API. Extend class ini di controller API untuk mendapat shortcut method `success()`, `error()`, `created()`, `paginated()`, `validateApi()`, dll.
3. **`JwtGuard`** (`src/Security/JwtGuard.php`): Implementasi JWT HS256 bawaan — `generate()`, `verify()`, `getUserId()`, `decode()`. Konfigurasi via `APP_JWT_SECRET` dan `APP_JWT_TTL` di `.env`.
4. **`JwtMiddleware`** (`src/Security/JwtMiddleware.php`): Middleware autentikasi JWT untuk route group. Payload token disuntikkan ke `$_SERVER['JWT_PAYLOAD']`.

### Komponen yang Diperbarui:
5. **`ApiAuthMiddleware`** (`src/Security/ApiAuthMiddleware.php`): Kini menggunakan `ApiResponse::unauthorized()` untuk respons error yang konsisten, mendukung multi-token via `APP_API_TOKENS` (comma-separated), dan backward-compatible dengan `APP_API_TOKEN` lama.
6. **`helpers.php`** (`src/helpers.php`): Ditambahkan 6 helper global baru: `api_success()`, `api_error()`, `api_paginated()`, `jwt_generate()`, `jwt_verify()`, `bearer_token()`.
7. **`routes/api.php`**: Template lengkap dengan 3 grup: Public (CORS only), Token Auth, dan JWT Auth. Endpoint baru: `GET /api/status`, `GET /api/ping`, `POST /api/connect`, `GET /api/me`.
8. **`.env` & `.env.example`**: Ditambahkan blok konfigurasi `APP_API_TOKEN`, `APP_API_TOKENS`, `APP_JWT_SECRET`, `APP_JWT_TTL`.

### Backfire CLI:
9. **`php backfire jwt:secret`**: Generate dan simpan `APP_JWT_SECRET` ke `.env` secara otomatis.
10. **`php backfire make:api-controller`**: Scaffold controller API baru yang extend `ApiController` dengan method CRUD siap pakai.

### Autoload:
11. Namespace `LunoxHoshizaki\Api\` ditambahkan di `composer.json` PSR-4 autoload.

---

## 🚀 v2.2.0 - Minor Enhancements & Stability Update (2026-05-05)

Update **v2.2.0** membawa serangkaian peningkatan kecil yang meningkatkan stabilitas, developer experience, dan konsistensi komponen inti framework.

### Perubahan & Peningkatan:
1. **Version Bump:** Seluruh titik registrasi versi (`composer.json`, `.env`, `.env.example`) diselaraskan ke `2.2.0` untuk konsistensi lintas komponen.
2. **Backfire CLI:** Banner versi pada `php backfire` dan `php backfire monitor` kini membaca langsung dari `APP_VERSION` di `.env`, sehingga tidak ada hardcoded fallback yang ketinggalan.
3. **Dokumentasi Internal:** Changelog di `resources/docs/VERSIONING.md` dan `V2_UPDATE_NOTES.md` diperbarui mencerminkan rilis ini.

---

## 🛡️ v2.1.0 - Security Hardening & Compliance Update (2026-04-13)

Update **v2.1.0** berfokus pada penutupan *security gaps* untuk mematuhi standar ISO 27001, OWASP Top 10 (2021), dan NIST SP 800-53, menjadikan framework ini sangat aman untuk level *enterprise*.

### Resolusi Keamanan Utama:
1. **File Upload Security (R1):** Penguatan method `storeFile()` dengan *dual-validation*. Kini menggunakan `finfo` untuk mendeteksi *MIME type* asli dari file, plus validasi ekstensi berdasarkan *allowlist* internal (`$allowedMimeTypes`). Hal ini menambal celah CWE-434. (File: `src/Http/Request.php`)
2. **Security Event Auto-Logging (R2):** Implementasi Audit Trail terotomatisasi. Seluruh percobaan login (sukses/gagal), logouts, dan *account lockout* otomatis dicatat via `Log`. (File: `src/Auth/Auth.php`)
3. **Account Lockout Mechanism (R3):** Fitur pertahanan terhadap *Brute-Force* dan *Credential Stuffing* (CWE-307). Akun akan terkunci otomatis (15 menit) jika 5 kali gagal login. (File: `src/Auth/Auth.php`)
4. **CORS Default Hardening (R4):** Pengaturan *Cross-Origin Resource Sharing* diperketat. *Wildcard* (`*`) dihilangkan sebagai default dan diganti menjadi larangan (*block-all*). Mendukung *dynamic origin resolution* dari variabel `.env` (`CORS_ALLOWED_ORIGIN`). (File: `src/Security/CorsMiddleware.php`)
5. **Password Complexity & New Validation Rules (R5):** Penambahan *rules* validasi kuat bawaan:
   - `password`: Validasi 8 karakter, kapital, huruf kecil, dan angka.
   - `password_strength`: Sama seperti atas, namun min 12 karakter dan butuh karakter spesial (NIST AAL2+).
   - `mimes` & `max_size`: Validasi ukuran dan ekstensi file langsung via `$request->validate()`. (File: `src/Validation/Validator.php`)
6. **RBAC Foundation (R6):** Dasar Role-Based Access Control ditambahkan pada auth. Termasuk fungsi bawaan `Auth::hasRole()`, `Auth::can()`, `Auth::requireRole()`, dan `Auth::requirePermission()`. (File: `src/Auth/Auth.php`)

---

## 🚀 v2.0.0 - Major Refactoring & Features (2026-04-09)

Pembaruan besar v2.0.0 (dirilis 2026-04-09) untuk codebase `lunoxhoshizaki-lite` membawa banyak refaktor dari segi keamanan, fitur inti framework, serta utilitas tambahan yang menjadikan framework ini lebih mendekati standar industri (mirip Laravel).

Berikut adalah ringkasan terkait file apa saja yang berubah dan fitur-fitur baru yang ditambahkan:

## 🔴 Security & Critical Patches
Perbaikan keamanan yang diimplementasikan untuk memenuhi standar ISO dan OWASP:
- **Session Fixation (CWE-384):** Perbaikan kerentanan fiksasi sesi — `Auth::login()` dan `logout()` sekarang memperbarui ID sesi dengan `session_regenerate_id(true)`. (File: `src/Auth/Auth.php`)
- **PHP Object Injection (CWE-502):** Mengganti `unserialize()` dengan JSON encoding/decoding di sistem Cache untuk mendongkrak keamanan dari eksploitasi Remote Code Execution (RCE). (File: `src/Cache/Cache.php`)
- **Secure Headers (OWASP A02):** Menambahkan header `Content-Security-Policy` dan `Permissions-Policy` ke dalam `SecureHeadersMiddleware`. (File: `src/Security/SecureHeadersMiddleware.php`)

## ⭐ Fitur-Fitur Baru yang Ditambahkan
Banyak komponen *core* mendapatkan perombakan masif:

### 1. Sistem Inti & Utilitas
- **Logging System:** Penambahan fitur log (`Log::info()`, `Log::error()`) dengan rotasi harian. (File: `src/Log/Log.php`)
- **Hash Helper:** Helper Kriptografi (`Hash::make()`, `Hash::check()`) mendukung struktur Argon2ID dan BCrypt. (File: `src/Security/Hash.php`)
- **Config Helper:** Mengakses nilai konfigurasi dengan notasi titik (`config('app.name')`). (File: `src/Config/Config.php`)
- **Utility Classes:** Menambahkan kelas pemroses tipe data yakni `Str` untuk manipulasi string, dan `Collection` array statis yang kaya akan fungsi bawaan. (File: `src/Support/Str.php`, `src/Support/Collection.php`)

### 2. HTTP, Routing, dan Validasi
- **Route Enhancements:** 
  - Route Grouping yang men-support *shared middleware* dan *prefix* (`Router::prefix()->group()`).
  - Named Routes, sekarang kamu bisa memberi nama `->name('login')` serta memanggil url menggunakan helper `route('login')`.
  (File: `src/Routing/Router.php`, `routes/web.php`, `routes/api.php`)
- **Request & Response:**
  - Fungsi penarik data dinamis (*typed request methods*): `->string()`, `->integer()`, `->boolean()`, `->file()`.
  - Sistem otomatis mengekstrak *body* JSON masuk. 
  - Redirect Helper canggih (`redirect()`, `back()`) yang nge-support penempelan *flash session*.
  (File: `src/Http/Request.php`, `src/Http/Redirect.php`)
- **Validator Kustomisasi:**
  - Penambahan 20+ *rules* khusus untuk divalidasi (regex, unique, exists, date, url, boolean, array, numeric, dan masih banyak lagi). (File: `src/Validation/Validator.php`)

### 3. Database & Models
- **Database Transactions:** Eksekusi ganda secara transaksional lewat `DB::transaction()`. (File: `src/Database/DB.php`)
- **Model Enhancements:** Metode ORM baru nan sakti seperti `whereIn`, `whereNull`, `$hidden`, `leftJoin`, `toArray`, `toJson`, dan `update`. (File: `src/Database/Model.php`)
- **Skema Lanjut:** Pembawa Skema (`Blueprint`) kini mendukung `table()` alter data, `rename()`, `hasTable()`, dan `hasColumn()`. (File: `src/Database/Schema/Blueprint.php`, `src/Database/Schema/Schema.php`)

### 4. Lingkungan Console (Artisan) & Helpers
- Ditambahkan perintah generator baru via terminal: `php backfire make:request`
- Perluasan daftar Helper Global seperti `route()`, `collect()`, `env()`, `now()`, `dd()`, `dump()`, `url()`, dsb. (File: `src/helpers.php`)

---
**⚠️ Perhatian (*Breaking Changes*):**
Oleh karena sistem Cache sekarang menggunakan `json_encode` ketimbang serialisasi native PHP, maka memori Cache lama akan tidak kompatibel dan *error*. **Sangat disarankan menjalankan `php backfire cache:clear` setelah Anda melakukan upgrade ke versi ini!**

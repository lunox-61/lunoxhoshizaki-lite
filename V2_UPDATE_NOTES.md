# Lunox Backfire v2 Update Notes

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

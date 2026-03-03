<?php

namespace App\Models;

use LunoxHoshizaki\Database\Model;

class User extends Model
{
    /**
     * Tentukan nama tabel jika berbeda dengan plural nama class.
     * Secara otomatis ini akan menjadi "users", jadi baris ini opsional.
     */
    protected string $table = 'users';

    /**
     * Tentukan primary key dari tabel ini.
     * Defaultnya adalah "id", jadi baris ini juga opsional.
     */
    protected string $primaryKey = 'id';

    /**
     * Tentukan kolom mana saja yang boleh dimodifikasi (Mass Assignment Protection).
     * Contoh: Anda tidak ingin kolom 'role' bisa diisi sembarangan lewat `User::create($_POST)`
     */
    protected array $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * --- CONTOH CUSTOM METHOD ---
     * Anda bisa menaruh logika bisnis di dalam Model
     */
    
    // Mengecek apakah user ini adalah admin
    public function isAdmin(): bool
    {
        return $this->role === 'Administrator';
    }

    // Mendapatkan semua user yang aktif berdasarkan field `is_active`
    public static function getActiveUsers()
    {
        return static::where('is_active', '=', 1)->get();
    }
}

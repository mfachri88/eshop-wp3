<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'customer';

    // ✅ PERBAIKAN: Hanya field yang ada di tabel customer
    protected $fillable = [
        'user_id',
        'nama',           // Bisa null, untuk nama tambahan customer
        'alamat',
        'pos',
        'google_id',
        'google_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($customer) {
            // Hapus user yang terkait ketika customer dihapus
            if ($customer->user) {
                $customer->user->delete();
            }
        });
    }

    protected $hidden = [
        'google_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
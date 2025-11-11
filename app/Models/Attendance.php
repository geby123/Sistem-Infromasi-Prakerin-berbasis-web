<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'date', 'status']; // Kolom yang bisa diisi secara massal

     // Definisikan kolom yang perlu di-cast menjadi tipe datetime
     protected $casts = [
        'date' => 'datetime',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

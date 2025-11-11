<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    use HasFactory;

    protected $fillable = [
        'upload_date',
        'week',
        'file_path',
        'user_id', // Pastikan kolom user_id ditambahkan
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}

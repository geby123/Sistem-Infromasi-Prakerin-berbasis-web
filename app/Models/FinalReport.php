<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_path',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPending()
    {
        return $this->status === 'menunggu verifikasi';
    }

    public function isVerified()
    {
        return $this->status === 'terverifikasi';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}

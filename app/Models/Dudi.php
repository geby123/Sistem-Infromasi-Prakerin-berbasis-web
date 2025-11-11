<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dudi extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'company',
        'city',
        'job_type',
        'status',
        'qualifications',
        'closing_date',
        'job_description',
        'max_total',
    ];
    public function users()
    {
        return $this->belongsToMany(User::class, 'applications', 'dudi_id', 'user_id')
                    ->withTimestamps();
    }
}

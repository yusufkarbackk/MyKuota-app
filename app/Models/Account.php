<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $useTimestamps = true;

    protected $fillable = [
        'username',
        'password',
        'phone_number',
        'status',
        'quota',
        'chrome_profile',
        'profile_path',
        'update_status',
        'error_log',
        'is_complete'
    ];

    public function site()
    {
        return $this->hasOne(Site::class);
    }
}

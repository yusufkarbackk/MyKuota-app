<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogTerminate extends Model
{
    use HasFactory;

    protected $useTimestamps = true;

    protected $fillable = [
        'account_id',
        'reason',
        'terminator_email',
    ];
}

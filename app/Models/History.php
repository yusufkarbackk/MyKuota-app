<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $useTimestamps = true;

    protected $fillable = [
        'account_id',
        'quota_usage',
        'quota',
        'flag'
    ];
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}

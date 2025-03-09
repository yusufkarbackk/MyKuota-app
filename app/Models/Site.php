<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $useTimestamps = true;

    protected $fillable = [
        'site',
        'company',
        'account_id',
        'usage',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}

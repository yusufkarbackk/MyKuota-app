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
        'phone_number',
        'quota',
        'action',
    ];
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}

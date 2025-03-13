<?php

namespace App\Models;

use App\Jobs\RunCreateSiteCommand;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;

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

    protected static function boot()
    {
        parent::boot();

        static::created(function ($site) {
            // ✅ Load the account relationship explicitly
            $site->load('account');

            if ($site->account) {
                //dd($site->account);
                dispatch(new RunCreateSiteCommand(
                    $site->account->username,
                    $site->account->password,
                    $site->account->id
                ));
            }
        });
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}

<?php

namespace Tests\Query;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Site;
use DB;

class QueryTesting extends Controller
{
    public function queryGetSites()
    {
        DB::enableQueryLog();

        $sites = Site::with('account')->get();
        // Get query log
        $log = DB::getQueryLog();
        echo "Query get sites";
        dump("query: " . $log[0]['query']);
        dump("time: " . $log[0]['time'] / 1000);
    }

    public function queryGetAccounts()
    {
        DB::enableQueryLog();

        $acc = Account::all();
        
        $log = DB::getQueryLog();
        echo "Query get accounts";
        dump("query: " . $log[0]['query']);
        dump("time: " . $log[0]['time'] / 1000);
    }

    public function testQuery()
    {
        //$this->queryGetSites();
        $this->queryGetAccounts();
    }
}
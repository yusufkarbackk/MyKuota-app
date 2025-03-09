<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        $data = [
            'sites' => Site::with('account')->get()
        ];
        return view('sites.index', $data);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\AccountServices;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    protected $accountService;

    public function __construct(AccountServices $accountService)
    {
        $this->accountService = $accountService;
    }
    public function index()
    {
        $data = [
            'accounts' => Account::all()
        ];
        return view('accounts.index', $data);
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        return $this->accountService->store($request);
    }
} 

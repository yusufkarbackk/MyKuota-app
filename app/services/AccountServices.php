<?php

namespace App\Services;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
class AccountServices
{
    public function store(Request $request)
    {
        $request->validate([
            'phone_number' => ['required', 'regex:/^08\d{8,12}$/'],
            'username' => 'required',
            'password' => 'required|min:6'
        ]);

        try {
            Account::create([
                'phone_number' => $request->phone_number,
                'username' => $request->username,
                'password' => Crypt::encryptString($request->password)
            ]);
            return redirect()->to(path: '/accounts')->with('success', 'Account created successfully!');
        } catch (\Throwable $th) {
            return redirect()->to('/accounts')->with('error', 'An error occurred while creating the account: ' . $th->getMessage());
        }
    }
}
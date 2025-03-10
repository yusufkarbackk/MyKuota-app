<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\AccountServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

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

    public function createCSV()
    {
        return view('accounts.createCSV');
    }

    public function store(Request $request)
    {
        return $this->accountService->store($request);
    }

    public function storeCSV(Request $request)
    {
        return $this->accountService->storeCSV($request);
    }

    public function edit($id)
    {
        $account = Account::findOrFail($id);
        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'phone_number' => ['required', 'regex:/^08\d{8,12}$/'],
            'username' => 'required',
            'password' => 'nullable|min:6'
        ]);

        try {
            $account = Account::findOrFail($id);

            // Prepare an empty array for updated data
            $updateData = [];

            // Only update changed values
            if ($request->phone_number !== $account->phone_number) {
                $updateData['phone_number'] = $request->phone_number;
            }

            if ($request->username !== $account->username) {
                $updateData['username'] = $request->username;
            }

            if ($request->filled('password')) { // Only update if password is provided
                $updateData['password'] = Crypt::encryptString($request->password);
            }

            // Only update if there are changes
            if (!empty($updateData)) {
                $account->update($updateData);
            }

            return redirect()->route('accounts.index')->with('success', 'Account updated successfully!');
        } catch (\Throwable $th) {
            return redirect()->route('accounts.index')->with('error', 'Error updating account: ' . $th->getMessage());
        }
    }
}

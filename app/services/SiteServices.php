<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SiteServices
{
    public function store(Request $request)
    {
        $request->validate([
            'account_id' => ['required'],
            'site' => 'required|unique:sites,site',
            'company' => 'required|'
        ]);

        try {
            Site::create([
                'site' => $request->site,
                'company' => $request->company,
                'account_id' => $request->account_id,
            ]);

            // Update account status to "in use"
            $account = Account::find($request->account_id);
            if ($account) {
                $account->update(['status' => 'in use']);
            }
            return redirect()->to(path: '/sites')->with('success', 'Site created successfully!');

        } catch (\Throwable $th) {
            return redirect()->to('/sites')->with('error', 'An error occurred while creating the account: ' . $th->getMessage());
        }
    }

    public function storeCSV(Request $request)
    {
        $csvData = [];

        // Handle file upload
        if (!$request->hasFile('csv_file')) {
            return redirect()->to(path: '/sites')->with('error', 'No file uploaded');
        }

        $file = $request->file('csv_file');

        if (!$file->isValid()) {
            return redirect()->to(path: '/accounts')->with('error', 'invalid file upload');
        }

        // Store the file in storage/app/uploads
        $filePath = $file->storeAs('uploads', $file->hashName());

        if (($handle = fopen(storage_path("app/$filePath"), "r")) !== false) {
            $headers = fgetcsv($handle, 0, ';');

            try {
                while (($row = fgetcsv($handle, 0, ';')) !== false) {
                    $csvData[] = array_combine($headers, $row);
                    $lastRow = end($csvData);

                    $site = trim($lastRow['Site']);
                    $company = trim($lastRow['Company']);
                    $phoneNumber = trim($lastRow['Nomor']);
                    $username = trim($lastRow['Username']);
                    $password = trim($lastRow['Password']);

                    // Check if site already exists
                    if (Site::where('site', $site)->exists()) {
                        return redirect()->to(path: '/sites')->with('error', $site . ' already exists');
                    }

                    // Check if account exists
                    $existingAccount = Account::where('phone_number', $phoneNumber)->first();

                    if ($existingAccount) {
                        // If number exists but is 'in use', reject it
                        if ($existingAccount->status == 'in use') {
                            return redirect()->to(path: '/sites')->with('error', "{$existingAccount->phoneNumber} is in use");
                        } else {
                            // Assign existing account to new site
                            $site = Site::create([
                                "site" => $site,
                                "company" => $company,
                                "account_id" => $existingAccount->id
                            ]);

                            // Update account status
                            $existingAccount->update(['status' => 'in use']);
                        }
                    } else {
                        // Encrypt password before storing
                        $encryptedPassword = Crypt::encryptString($password);

                        // Create new account
                        $newAccount = Account::create([
                            "phone_number" => $phoneNumber,
                            "username" => $username,
                            "password" => $encryptedPassword
                        ]);

                        // Create new site linked to the new account
                        Site::create([
                            "site" => $site,
                            "company" => $company,
                            "account_id" => $newAccount->id
                        ]);

                        // Update new account status
                        $newAccount->update(['status' => 'in use']);
                    }
                }

                // // Run Windows Task Scheduler command (Ensure task exists)
                // $taskName = 'run spark command';
                // exec("schtasks /run /tn \"$taskName\"");

                return redirect()->to(path: '/sites')->with('success', 'Sites created successfully!');

            } catch (\Throwable $th) {
                return redirect()->to('/sites')->with('error', $th->getMessage());
            }
        }
    }

}
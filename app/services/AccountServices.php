<?php

namespace App\Services;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class AccountServices
{
    public function decryptPassword($password)
    {
        return Crypt::decryptString($password);
    }
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

    public function storeCSV(Request $request)
    {
        // Validate file input
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        try {
            // Handle file upload
            $file = $request->file('csv_file');
            $filePath = $file->store('uploads'); // Stores in storage/app/uploads

            // Open and read the file
            $fileStream = Storage::path($filePath);
            if (($handle = fopen($fileStream, "r")) === false) {
                return redirect()->to('/accounts')->with('failed', 'Failed to read the file');
            }

            $headers = fgetcsv($handle, 0, ','); // Read CSV headers
            $bulkToInsert = [];

            DB::beginTransaction(); // Begin transaction

            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                $csvData = array_combine($headers, $row);
               
                $phoneNumber = $csvData['Nomor'] ?? null;
                $username = $csvData['Username'] ?? null;
                $password = $csvData['Password'] ?? null;
                if (!$phoneNumber || !$username || !$password) {
                    fclose($handle);
                    return redirect()->to('/accounts')->with('failed', 'Invalid CSV format');
                }

                // Check if account exists
                if (Account::where('phone_number', $phoneNumber)->exists()) {
                    fclose($handle);
                    return redirect()->to('/accounts')->with('failed', "{$phoneNumber} already exists");
                }

                // Prepare data for batch insert
                $bulkToInsert[] = [
                    "phone_number" => $phoneNumber,
                    "username" => $username,
                    "password" => Crypt::encryptString($password),
                    "created_at" => now(),
                    "updated_at" => now(),
                ];
            }

            fclose($handle); // Close file

            // Batch Insert
            if (!empty($bulkToInsert)) {
                Account::insert($bulkToInsert);
            }

            DB::commit(); // Commit transaction

            return redirect()->to('/accounts')->with('success', 'Accounts created successfully');

        } catch (\Throwable $th) {
            DB::rollBack(); // Rollback on error
            return redirect()->to('/accounts')->with('failed', 'Error: ' . $th->getMessage());
        }
    }
}





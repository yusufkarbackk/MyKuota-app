<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\History;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Log;

class manualUpdateData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:manual-update-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $accountModel = new Account();
        $accounts = Account::where('status', '=', 'in use')
            ->where('update_status', '=', 'failed')
            ->where('is_complete', '=', true)
            ->get()
            ->toArray();    
        try {
            foreach ($accounts as $account) {
                $site = trim($account['Site']);
                $phoneNumber = trim($account['phone_number']);
                $username = trim($account['username']);
                $password = trim($account['password']);
                $currentQuota = trim($account['quota']);
                $currentUsage = trim($account['total_usage']);
                $decryptedPassword = Crypt::decryptString($password);

                $command = "python " . escapeshellarg("C:\\xampp\\MyKuota-script\\update_client.py") . " " .
                    escapeshellarg($username) . " " .
                    escapeshellarg($decryptedPassword);

                $result = shell_exec($command);
                dump("Result: " . print_r($result, true));

                $resultData = json_decode($result, true);

                if ($resultData['status'] == 'success') {
                    if ($resultData['quota'] > $currentQuota) {
                        try {
                            $accountModel->where('id', $account['id'])->update([
                                'quota' => $resultData['quota'],
                            ]);

                            History::create([
                                'account_id' => $account['id'],
                                'quota' => $resultData['quota'],
                                'flag' => 'topup'
                            ]);

                            Log::info(message: "Username: {$username} updated successfully");

                        } catch (\Throwable $th) {
                            Log::error("Error updating data for Username: {$username} {$th->getMessage()}");
                        }
                    } else {
                        $updatedUsage = $currentUsage + ($currentQuota - $resultData['quota']);
                        try {
                            $accountModel->where('id', $account['id'])->update([
                                'quota' => $resultData['quota'],
                                'total_usage' => $updatedUsage,
                            ]);

                            History::create([
                                'account_id' => $account['id'],
                                'quota' => $resultData['quota'],
                                'quta_usage' => $currentQuota - $resultData['quota']
                            ]);

                            Log::info(message: "Username: {$username} updated successfully");

                        } catch (\Throwable $th) {
                            Log::error("Error updating data for Username: {$username} {$th->getMessage()}");
                        }
                    }
                } else {
                    $accountModel->where('id', $account['id'])->update([
                        'update_status' => 'failed',
                        'error_log' => $resultData['message'],
                        'complete' => 'complete',
                        'chrome_profile' => $resultData['chrome_profile'],
                    ]);
                    Log::error("Playwright script failed for Username: {$username}");
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}

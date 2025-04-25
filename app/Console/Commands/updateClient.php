<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\History;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Log;

class updateClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-client';

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
            ->where('is_complete', '=', true)
            ->get()
            ->toArray();
        if (empty($accounts)) {
            Log::info('no data');
        } else {
            try {
                foreach ($accounts as $account) {
                    $username = trim($account['username']);
                    $password = trim($account['password']);
                    $currentQuota = trim($account['quota']);
                    $currentUsage = trim($account['total_usage']);
                    $decryptedPassword = Crypt::decryptString($password);

                    $command = "python " . escapeshellarg("C:\\Users\\Administrator\\dev\\MyKuota-script\\update_client.py") . " " .
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
                            Log::info(gettype($currentUsage));
                            Log::info(message: $currentUsage);

                            Log::info(gettype($currentQuota));
                            Log::info(message: $currentQuota);

                            Log::info(gettype($resultData['quota']));
                            Log::info(message: $resultData['quota']);

                            $updatedUsage = $currentUsage + ($currentQuota - $resultData['quota']);
                            try {
                                $accountModel->where('id', $account['id'])->update([
                                    'quota' => $resultData['quota'],
                                    'total_usage' => $updatedUsage,
                                ]);

                                History::create([
                                    'account_id' => $account['id'],
                                    'quota' => $resultData['quota'],
                                    'quota_usage' => $currentQuota - $resultData['quota']
                                ]);

                                Log::info("Username: {$username} updated successfully");

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
                        Log::error("Update failed for Username: {$username}");
                    }
                }
            } catch (\Throwable $th) {
                //throw $th;
                Log::error("error update: {$th->getMessage()} : {$th->getLine()}");
            }
        }

    }
}

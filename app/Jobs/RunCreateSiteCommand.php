<?php

namespace App\Jobs;

use App\Models\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Crypt;
use Log;

class RunCreateSiteCommand implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $username;
    protected $password;
    protected $accountId;


    /**
     * Create a new job instance.
     */
    public function __construct($username, $password, $accountId)
    {
        $this->username = $username;
        $this->password = $password;
        $this->accountId = $accountId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $accountModel = new Account();

        // Fetch arguments
        $username = $this->username;
        $account_id = $this->accountId;
        $password = $this->password;

        try {
            Log::error("Status: " . print_r($username, true));

            $decryptedPassword = Crypt::decryptString($password);
            //$this->cliWrite("Username: {$username} - Password: {$decryptedPassword} - ID: {$account_id} run for script", 'yellow');

            // Execute the Python script
            $command = "python C:\\laragon\\www\\dev\\MyKuota-script\\create_new_client.py " .
                escapeshellarg($username) . " " .
                escapeshellarg($decryptedPassword);
            $result = shell_exec($command);

            $resultData = json_decode($result, true);
            Log::error("Status: " . print_r($resultData, true));

            if ($resultData['status'] == 'success') {
                if (
                    $accountModel->where('id', $account_id)->update([
                        'quota' => $resultData['quota'],
                        'chrome_profile' => $resultData['chrome_profile'],
                        'profile_path' => $resultData['profile_path'],
                        'complete' => 'complete',
                        'update_status' => 'success'
                    ])
                ) {
                    // $this->cliWrite("Username: {$username} updated successfully", 'green');
                    Log::info("Username: {$username} updated successfully");
                } else {
                    //$this->cliWrite("Error updating data for Username: {$username}", 'red');
                    Log::error("Error updating data for Username: {$username}");
                }
            } else {
                $accountModel->where('id', $account_id)->update([
                    'update_status' => 'failed',
                    'error_log' => $resultData['message'],
                    'complete' => 'complete',
                    'chrome_profile' => $resultData['chrome_profile'],
                ]);
                Log::error("Playwright script failed for Username: {$username}");
            }
        } catch (\Throwable $th) {
            Log::error("Error: {$th->getTraceAsString()} - {$th->getMessage()}");
        }
    }
}

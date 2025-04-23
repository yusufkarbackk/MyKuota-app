<?php

namespace App\Console\Commands;

use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Crypt;
use Log;

class CreateSites extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-site:run {username} {password} {account_id}';

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

        // Fetch arguments
        $username = $this->argument('username');
        $password = $this->argument('password');
        $account_id = $this->argument('account_id');

        try {
            dump("Username: {$username} - Password: {$password} run for script");

            $decryptedPassword = Crypt::decryptString($password);

            // Execute the Python script
            $command = "python " . escapeshellarg("C:\\laragon\\www\\dev\\MyKuota-script\\create_new_client.py") . " " .
                escapeshellarg($username) . " " .
                escapeshellarg($decryptedPassword);
            $result = shell_exec($command);
            dump("Result: " . print_r($result, true));

            $resultData = json_decode($result, true);

            //Log::error("Status: " . print_r($resultData, true));

            if ($resultData['status'] == 'success') {
                if (
                    $accountModel->where('id', $account_id)->update([
                        'quota' => $resultData['quota'],
                        'chrome_profile' => $resultData['chrome_profile'],
                        'profile_path' => $resultData['profile_path'],
                        'is_complete' => true,
                        'update_status' => 'success'
                    ])
                ) {
                    $this->cliWrite("Username: {$username} updated successfully", 'green');
                    Log::info("Username: {$username} updated successfully");
                } else {
                    $this->cliWrite("Error updating data for Username: {$username}", 'red');
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

    private function cliWrite($message, $color)
    {
        if (app()->runningInConsole()) {
            $colorMap = [
                'yellow' => 'comment',
                'green' => 'info',
                'red' => 'error'
            ];
            Artisan::call('line', ['message' => $message, '--style' => $colorMap[$color] ?? 'info']);
        }
    }
}


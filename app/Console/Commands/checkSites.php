<?php

namespace App\Console\Commands;

use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Log;

class checkSites extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-sites';

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
        $accounts = Account::where('is_complete', '=', false)
            ->where('status', '=', 'in use')
            ->where('update_status', '=', 'failed')
            ->where(function ($query) {
                $query->where('error_log', '!=', '')
                    ->orWhere('error_log', '=', '');
            })
            ->get()
            ->toArray();

        if (empty($accounts)) {
            dump("No accounts to check");
        } else {
            foreach ($accounts as $account) {

                try {
                    dump("Creating account: " . $account['username']);

                    $decryptedPassword = Crypt::decryptString($account['password']);
                    $result = exec(
                        'python ' . getenv("SCRIPT_PATH") . "create_new_client.py " .
                        escapeshellarg($account['username']) . " " .
                        escapeshellarg($decryptedPassword)
                    );

                    $resultData = json_decode($result, true);
                    //log_message('error', "status: " . print_r($resultData, true));
                    dump($resultData);
                    $updateData = [];
                    if ($resultData['status'] == 'success') {
                        //die($result);
                        $updateData = [
                            'quota' => $resultData['quota'],
                            'chrome_profile' => $resultData['chrome_profile'],
                            'profile_path' => $resultData['profile_path'],
                            'complete' => 'complete',
                            'update_status' => 'success',
                        ];
                    } else {
                        $updateData = [
                            'update_status' => 'failed',
                            'error_log' => $resultData['message'],
                        ];
                    }
                    $accountWillUpdate = Account::find($account['id']);
                    $accountWillUpdate->update($updateData);

                } catch (\Throwable $th) {
                    Log::error("Error in checking account: " . $account['username'] . " - " . $th->getMessage());
                }
            }
        }

    }
}

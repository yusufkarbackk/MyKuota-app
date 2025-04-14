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
        $accounts = Account::where('is_complete', '=', 0)
            ->where('status', '=', 'in use')
            ->get()
            ->toArray();

        if (empty($accounts)) {
            $this->cliWrite("No accounts to check", 'yellow');
        } else {
            foreach ($accounts as $account) {

                try {
                    dump("Checking account: " . $account['username']);

                    $decryptedPassword = Crypt::decryptString($account['password']);
                    $result = shell_exec(
                        'python C:\\laragon\\www\\dev\\MyKuota-script\\create_new_client.py' . " " .
                        escapeshellarg($account['username']) . " " .
                        escapeshellarg($decryptedPassword)
                    );

                    $resultData = json_decode($result, true);
                    //log_message('error', "status: " . print_r($resultData, true));

                    if ($resultData['status'] == 'success') {
                        //die($result);

                        $account->quota = $resultData['quota'];
                        $account->chrome_profile = $resultData['chrome_profile'];
                        $account->profile_path = $resultData['profile_path'];
                        $account->complete = 'complete';
                        $account->update_status = 'success';

                    } else {
                        $account->update_status = 'failed';
                        $account->error_log = $resultData['message'];

                        //log_message('error', "Playwright script failed for data ID: {$account['username']}");
                    }
                } catch (\Throwable $th) {
                    Log::error("Error in checking account: " . $account['username'] . " - " . $th->getMessage());
                }
            }
        }

    }
}

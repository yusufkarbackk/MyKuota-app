<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\History;
use App\Models\Site;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Log;

class updateSitesData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-sites-data';

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
        $totalProfiles = Account::where('is_complete', '=', 1)
            ->where('status', 'in use')
            ->count();

        if ($totalProfiles < 1) {
            $this->warn("No data found");
            return;
        }

        $this->info("Total username: {$totalProfiles} run for update");

        $batchSize = 10;

        Account::where('is_complete', '=', 1)
            ->where('status', 'in use')
            ->orderBy('id') // optional, for predictable batch processing
            ->chunk($batchSize, function ($profiles) {
                foreach ($profiles as $account) {
                    try {
                        $this->info("username: {$account->username} run for update");

                        $decryptedPassword = Crypt::decryptString($account->password); // Assuming Laravel's encrypt/decrypt
                        $command = escapeshellcmd("python C:\\laragon\\www\\dev\\MyKuota-script\\update_client.py " .
                            escapeshellarg($account->username) . " " .
                            escapeshellarg($decryptedPassword));

                        $result = shell_exec($command);
                        $resultData = json_decode($result, true);

                        if ($resultData["status"] === 'success') {
                            dump($resultData);
                            $newQuota = $resultData['quota'];

                            if ($newQuota > $account->quota) {
                                History::create([
                                    'account_id' => $account->id,
                                    'user' => 'system',
                                    'flag' => 'Top up',
                                    'quota' => $newQuota
                                ]);

                                $account->update([
                                    'update_status' => 'success',
                                    'quota' => $newQuota,
                                    'error_log' => ''
                                ]);
                            } else {
                                $usageData = Site::where('account_id', $account->id)->first();

                                $currentUsage = $account->quota - $newQuota;
                                $finalUsage = ($usageData->usage ?? 0) + $currentUsage;

                                History::create([
                                    'account_id' => $account->id,
                                    'user' => 'system',
                                    'quota' => $newQuota
                                ]);

                                $account->update([
                                    'update_status' => 'success',
                                    'quota' => $newQuota
                                ]);

                                Site::where('account_id', $account->id)
                                    ->update(['usage' => $finalUsage]);
                            }
                        } else {
                            $account->update([
                                'update_status' => 'failed',
                                'error_log' => $resultData['message']
                            ]);

                            Log::error("username: {$account->username} UPDATE FAILED");
                        }
                    } catch (\Throwable $th) {
                        $account->update([
                            'update_status' => 'failed',
                            'error_log' => $th->getMessage()
                        ]);

                        Log::error("Error: {$th->getMessage()} on line {$th->getLine()} in file {$th->getFile()}");
                    }
                }
            });

        // // Trigger scheduled task
        // $taskName = 'run check low quota sites';
        // $command = "schtasks /run /tn \"$taskName\"";
        // exec($command);

    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Site;
use App\Services\SiteServices;
use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Log;

class SiteController extends Controller
{
    protected $siteServices;

    public function __construct(SiteServices $siteServices)
    {
        $this->siteServices = $siteServices;
    }
    public function index()
    {
        $topUsage = Site::join('accounts', 'sites.account_id', '=', 'accounts.id')
            ->select('sites.id', 'sites.site', 'sites.company', 'accounts.total_usage')
            ->orderBy('accounts.total_usage', 'desc')
            ->limit(5)
            ->get();
        //$topUsage = Site::orderBy('usage', 'desc')->limit(5)->get();
        $sites = Site::select('sites.*') // ensures we get only Site model columns
            ->join('accounts', 'sites.account_id', '=', 'accounts.id')
            ->where('accounts.update_status', 'success')
            ->orderBy('accounts.quota', 'asc') // or 'asc'
            ->with('account')
            ->get();
        return view('sites.index', compact('sites', 'topUsage'));
    }

    public function show($id)
    {
        //dd($id);
        $repsonse = $this->siteServices->show($id);
        //dd($data);
        $data = json_decode($repsonse->getContent(), true);
        return view('sites.detail', compact('data'));
    }

    public function create()
    {
        $data = Account::select('id', 'phone_number')->where('status', 'available')->get();
        return view('sites.create', ['data' => $data]);

    }

    public function store(Request $request)
    {
        return $this->siteServices->store($request);
    }

    public function createCSV()
    {
        return view('sites.createCSV');

    }

    public function storeCSV(Request $request)
    {
        return $this->siteServices->storeCSV($request);
    }

    public function edit($id)
    {
        // Fetch the site and related account data
        $client = Site::select('sites.id AS site_id', 'sites.*', 'accounts.*')
            ->leftJoin('accounts', 'sites.account_id', '=', 'accounts.id')
            ->where('sites.id', $id)
            ->first();

        // Fetch available accounts
        $accounts = Account::select('id', 'phone_number')
            ->where('status', 'available')
            ->get();

        // Pass data to the view
        return view('sites.edit', compact('client', 'accounts'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $site = Site::with('account')->findOrFail($id); // Retrieve original data with account relationship
            $formData = $request->all();
            $changes = [];

            // Compare original data with the new data, and store only changes
            foreach ($formData as $key => $value) {
                if (isset($site->$key) && $site->$key != $value) {
                    $changes[$key] = $value;
                }
            }
            //dd($changes);

            // If there are any changes, update the database
            if (!empty($changes)) {
                if (isset($changes['site'])) {
                    $site->site = $changes['site'];
                }
                if (isset($changes['company'])) {
                    $site->company = $changes['company'];
                }
                if (isset($changes['account_id'])) {
                    $oldAccountId = $site->account_id;
                    $newAccountId = $changes['account_id'];

                    // Update site data with the new phone number
                    $site->update([
                        'account_id' => $newAccountId,
                        'usage' => 0
                    ]);

                    // Set old account as available
                    Account::where('id', $oldAccountId)->update([
                        'status' => 'available',
                        'quota' => NULL,
                        'is_complete' => "0",
                        'chrome_profile' => "",
                        'profile_path' => "",
                        'updated_at' => NULL,
                        'update_status' => "",
                        'error_log' => ""
                    ]);

                    // Set new account as in use
                    Account::where('id', $newAccountId)->update(['status' => 'in use']);

                    // // Insert into site history
                    // SiteHistory::create([
                    //     'site_id' => $id,
                    //     'account_id' => $newPhoneNumberId
                    // ]);

                    // Get new account credentials
                    $accountData = Account::select('username', 'password', 'id')->where('id', $newAccountId)->first();

                    // Commit transaction

                    // Run additional actions outside the transaction
                    // try {
                    //     $accountChromeProfile = Account::select('chrome_profile')->where('id', $oldAccountId)->first();

                    //     if ($accountChromeProfile && $accountChromeProfile->chrome_profile) {
                    //         app('App\Services\LibService')->deleteChromeProfile($accountChromeProfile->chrome_profile);
                    //     }

                    //     app('App\Services\LibService')->runCreateClient($accountData->username, $accountData->password, $accountData->id);
                    // } catch (\Throwable $th) {
                    //     Log::error("Error in additional actions: " . $th->getMessage());
                    // }
                }
                DB::commit();
                $site->save();
                //dd($site);// <-- Save the model

            }

            return redirect()->route('home')->with('success', 'Client updated successfully.');

        } catch (\Throwable $th) {
            DB::rollBack(); // Rollback transaction if error occurs
            return redirect()->route('home')->with('error', "Error: " . $th->getMessage());
        }
    }

    public function showTopUsage()
    {
        $data = Site::orderBy('usage', 'desc')->limit(5)->get();
    }

    public function delete($id): RedirectResponse
    {
        $site = Site::find($id);

        if (!$site) {
            return redirect()->to('/sites')->with('error', 'Item not found.');
        }

        $account = Account::where('id', $site->account_id)->first();

        if ($account) {
            $folderPath = "C:\\Users\\Administrator\\AppData\\Local\\BraveSoftware\\Brave-Browser\\User Data\\" . $account->chrome_profile;

            // Reset account details
            $account->update([
                'status' => 'available',
                'quota' => 0,
                'complete' => '',
                'chrome_profile' => "",
                'profile_path' => "",
                'created_at' => null,
                'updated_at' => null
            ]);

            // Delete site entry
            $site->delete();

            // Delete folder if profile exists
            if (!empty($account->chrome_profile) && File::exists($folderPath)) {
                File::deleteDirectory($folderPath);

                if (!File::exists($folderPath)) {
                    Log::info('Folder deleted successfully: ' . $folderPath);
                } else {
                    Log::error('Failed to delete folder: ' . $folderPath);
                }
            } else {
                Log::error('Directory does not exist: ' . $folderPath);
            }

            return redirect()->to('/sites')->with('success', 'Site deleted successfully');
        }

        return redirect()->to(path: '/sites')->with('error', 'Site not found');
    }

    public function bulkDelete(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer'
        ]);

        try {
            DB::beginTransaction();

            $sites = Site::whereIn('id', $request->ids)->get();

            // Collect all account IDs that need to be updated
            $accountIds = $sites->pluck('account_id')->filter()->unique()->toArray();

            // Update these accounts to available status
            Account::whereIn('id', $accountIds)
                ->update(['status' => 'available']);

            // Then delete the sites
            Site::whereIn('id', $request->ids)->delete();

            // Commit transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sites deleted and accounts updated successfully'
            ]);
        } catch (\Throwable $th) {
            // Roll back transaction if something goes wrong
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function showUnupdatedClients()
    {
        $sites = DB::table('sites')
            ->leftJoin('accounts', 'sites.account_id', '=', 'accounts.id')
            ->select('sites.*', 'accounts.*')
            ->where('accounts.update_status', 'failed')
            ->where('accounts.status', '!=', 'terminated')
            ->get();
        //dd($sites);
        return view('sites.unUpdatedSites', ['sites' => $sites]);
    }

    public function manualUpdatSites()
    {
        try {
            shell_exec('schtasks /run /tn "manual update"');
            return redirect()->to(path: '/sites')->with('success', 'Manual update is running');
        } catch (\Throwable $th) {
            return redirect()->to(path: '/sites')->with('error', 'An error occurred while updating data: ' . $th->getMessage());
        }
    }

}

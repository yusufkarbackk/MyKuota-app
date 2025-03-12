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
        $topUsage = Site::orderBy('usage', 'desc')->limit(5)->get();

        $sites = Site::with('account')->get();
        return view('sites.index', compact('sites', 'topUsage'));
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

    public function showUnupdatedClients()
    {
        $sites = DB::table('sites')
            ->leftJoin('accounts', 'sites.account_id', '=', 'accounts.id')
            ->select('sites.*', 'accounts.*')
            ->where('accounts.update_status', 'failed')
            ->where('accounts.status', '!=', 'terminated')
            ->get();

        return view('sites.unUpdatedSites', ['sites' => $sites]);
    }
}

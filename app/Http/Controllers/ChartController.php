<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JsonException;

class ChartController extends Controller
{
    public function getResultsCount(): JsonResponse
    {
        $success = Account::where('update_status', 'success')
            ->where('status', '!=', 'terminated')
            ->where('status', 'in use')
            ->count();

        $failure = Account::where('update_status', 'failed')
            ->where('status', '!=', 'terminated')
            ->where('status', 'in use')
            ->count();

        return response()->json([
            'success' => $success,  
            'failure' => $failure
        ], 200);
    }
}

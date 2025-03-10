<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function downloadAccountCSVTemplate()
    {
        // Set the headers for the file download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Accounts_Template.csv"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Open a file pointer to output the CSV data
        $output = fopen('php://output', 'w');

        // Write column headers
        fputcsv($output, ['Nomor', 'Username', 'Password'], ';');

        // Close the file pointer
        fclose($output);
        exit;
    }
}

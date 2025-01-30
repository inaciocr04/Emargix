<?php

namespace App\Http\Controllers;

use App\Imports\DataImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function showImporForm()
    {
        return view('dashboard');
    }
    public function import(Request $request)
    {

        $file = $request->file('file');

        $extension = $file->getClientOriginalExtension();
        try {
            if ($extension === 'csv') {
                Excel::import(new DataImport, $file, null, \Maatwebsite\Excel\Excel::CSV);
            } elseif (in_array($extension, ['xls', 'xlsx'])) {
                Excel::import(new DataImport, $file);
            } else {
                return redirect()->back()->with('error', 'Format de fichier non supporté.');
            }
            return redirect()->back()->with('success', 'Données importés avec succés !');

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'importation : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'importation.');
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Exports\AttendanceExport;
use App\Exports\AttendanceExportPdf;
use App\Models\AttendanceForm;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
class ExportController extends Controller
{
    public function export($eventId)
    {
        // Vérifie si l'eventId existe dans la base de données
        $attendanceForm = AttendanceForm::where('event_id', $eventId)->firstOrFail(); // Assurez-vous que l'ID existe

        // Générer le nom du fichier Excel
        $fileName = 'Emargement_' . str_replace(' ', '_', $attendanceForm->event_name) . '.xlsx';

        // Exporter le fichier Excel
        return Excel::download(new AttendanceExport($eventId), $fileName);
    }

    public function exportPdf($eventId)
    {
        // Créer une instance de la classe AttendanceExportPDF et appeler la méthode export
        $attendanceExportPDF = new AttendanceExportPdf($eventId);

        // Retourner le PDF généré en téléchargement
        return $attendanceExportPDF->export();
    }


}

<?php

namespace App\Exports;

use App\Models\Student;
use App\Models\AttendanceForm;
use App\Models\StudentSignature;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class AttendanceExportPdf
{
    protected $eventId;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    public function export()
    {
        $attendanceForm = AttendanceForm::where('event_id', $this->eventId)->first();

        if (!$attendanceForm) {
            return response()->json(['message' => 'Aucun formulaire de présence trouvé pour cet event_id'], 404);
        }

        // Récupérer les étudiants liés à cet événement
        $query = Student::where('training_id', $attendanceForm->training_id);

        if ($attendanceForm->course_id) {
            $query->where('course_id', $attendanceForm->course_id);
        }
        if ($attendanceForm->tp_group_id) {
            $query->where('tp_group_id', $attendanceForm->tp_group_id);
        }
        if ($attendanceForm->td_group_id) {
            $query->where('td_group_id', $attendanceForm->td_group_id);
        }

        $students = $query->orderBy('lastname', 'ASC')->get();

        // Récupérer toutes les signatures d'étudiants pour ce formulaire de présence
        $studentSignatures = StudentSignature::where('attendance_form_id', $attendanceForm->id)->get();

        $signatureTeacherData = null;
        $signatureRelativePath = str_replace('storage/', '', $attendanceForm->signature_teacher);

        // Utiliser public_path() pour obtenir le chemin dans le répertoire public
        $signatureFullPath = public_path('storage/' . $signatureRelativePath);

        if (file_exists($signatureFullPath) && is_file($signatureFullPath)) {
            $signatureTeacherData = 'data:image/png;base64,' . base64_encode(file_get_contents($signatureFullPath));
        } else {
            return redirect()->back()->with('error', 'Le fichier de signature du professeur est introuvable. Veuillez contacter le support.');
        }




        $studentSignaturesData = [];
        foreach ($studentSignatures as $signature) {
            $signatureData = null;
            $signatureRelativePathStudent = str_replace('storage/', '', $signature->signature);

            // Utiliser public_path() pour générer le chemin absolu correct dans le répertoire public
            $signatureFullPathStudent = public_path('storage/' . $signatureRelativePathStudent);

            if (file_exists($signatureFullPathStudent) && is_file($signatureFullPathStudent)) {
                $signatureData = 'data:image/png;base64,' . base64_encode(file_get_contents($signatureFullPathStudent));
            } else {
                return redirect()->back()->with('error', 'Le fichier de signature d\'un étudiant est introuvable. Veuillez contacter le support.');
            }

            $studentSignaturesData[$signature->student_id] = $signatureData;
        }



        // Générer le PDF
        $pdf = Pdf::loadView('exports.attendance_pdf', [
            'attendanceForm' => $attendanceForm,
            'students' => $students,
            'studentSignatures' => $studentSignatures,
            'studentSignaturesData' => $studentSignaturesData,
            'signatureTeacherData' => $signatureTeacherData,
        ]);

        return $pdf->download('attendance_report_' . $this->eventId . '.pdf');
    }


}

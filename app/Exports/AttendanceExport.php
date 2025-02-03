<?php

namespace App\Exports;

use App\Models\Student;
use App\Models\AttendanceForm;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Support\Facades\Storage;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected $eventId;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    public function collection()
    {
        $attendanceForm = AttendanceForm::where('event_id', $this->eventId)->first();

        if (!$attendanceForm) {
            return collect(); // Aucun formulaire de présence trouvé pour cet event_id
        }

        // Récupérer les étudiants
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

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Nom', 'Prénom', 'Formation', 'Parcours', 'Groupe de TD', 'Groupe de TP', 'Statut de signature', 'Signature'
        ];
    }

    public function map($student): array
    {
        $attendanceForm = AttendanceForm::where('event_id', $this->eventId)->first();
        $studentSignature = $student->signatures()->where('attendance_form_id', $attendanceForm->id)->first();

        $isSigned = $studentSignature !== null;
        $isAbsent = $student->student_statu == 'Absent';
        $isPresent = $student->student_statu == 'Present';

        return [
            $student->lastname, // Nom
            $student->firstname, // Prénom
            $student->training->name ?? 'N/A', // Formation
            $student->course->name ?? 'N/A', // Parcours
            $student->td_group->name ?? 'N/A', // Groupe de TD
            $student->tp_group->name ?? 'N/A', // Groupe de TP
            $isSigned ? '✔ Signé' : '✘ Absent', // Statut de signature
            $isAbsent ? '✔' : '', // Statut Absence
            $isPresent ? '✔' : '', // Statut Présence
            $this->getStudentSignatureImage($studentSignature) // Signature image (lien temporaire)
        ];
    }

    public function getStudentSignatureImage($studentSignature)
    {
        if ($studentSignature && $studentSignature->signature) {
            // Si la signature existe, récupérer le chemin du fichier
            return Storage::url($studentSignature->signature); // Lien temporaire pour le fichier image
        }
        return ''; // Pas de signature
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function ($event) {
                $attendanceForm = AttendanceForm::where('event_id', $this->eventId)->first();

                if ($attendanceForm) {
                    // Ajouter les informations de l'événement en haut de la feuille Excel
                    $sheet = $event->sheet;

                    // Informations de l'événement
                    $sheet->setCellValue('A1', 'Nom du Professeur : ' . $attendanceForm->teacher->name);
                    $sheet->setCellValue('A2', 'Nom de l\'événement : ' . $attendanceForm->event_name);
                    $sheet->setCellValue('A3', 'Horaire : ' . $attendanceForm->event_start_hour . ' - ' . $attendanceForm->event_end_hour);
                    $sheet->setCellValue('A4', 'Date : ' . $attendanceForm->event_date);
                }

                // Ajouter les images des signatures pour chaque étudiant
                $sheet = $event->sheet;
                $row = 6; // À partir de la ligne 6, où commencent les données des étudiants
                $students = $this->collection();

                foreach ($students as $student) {
                    $studentSignature = $student->signatures()->where('attendance_form_id', $attendanceForm->id)->first();
                    if ($studentSignature && $studentSignature->signature) {
                        $imagePath = storage_path('app/public/' . $studentSignature->signature);

                        if (file_exists($imagePath)) {
                            // Créer une image dans Excel
                            $drawing = new Drawing();
                            $drawing->setName('Signature ' . $student->firstname . ' ' . $student->lastname);
                            $drawing->setDescription('Signature');
                            $drawing->setPath($imagePath); // Chemin de l'image
                            $drawing->setHeight(50); // Hauteur de l'image
                            $drawing->setCoordinates('J' . $row); // Insérer l'image dans la colonne "J"
                            $drawing->setWorksheet($sheet);
                        }
                    }
                    $row++; // Incrémenter la ligne pour chaque étudiant
                }
            }
        ];
    }
}

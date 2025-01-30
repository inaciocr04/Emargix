<?php

namespace App\Livewire;

use App\Models\AttendanceForm;
use App\Models\Student;
use App\Models\StudentSignature;
use Livewire\Component;

class AttendanceEvent extends Component
{
    public $eventId;
    public $students = [];

    // Méthode de montage pour initialiser les données
    public function mount($eventId)
    {
        $this->eventId = $eventId;
        $this->loadStudents();
    }

    // Charger les étudiants selon leur training_id, group_id ou course_id
    public function loadStudents()
    {
        // Récupérer l'événement (pour avoir l'id de l'event)
        $attendanceForm = AttendanceForm::where('event_id', $this->eventId)->latest()->first();

        if ($attendanceForm) {
            // Initialiser la requête pour récupérer les étudiants
            $query = Student::where('training_id', $attendanceForm->training_id);

            // Ajouter un filtre pour le cours si l'attendanceForm a un course_id
            if ($attendanceForm->course_id) {
                $query->where('course_id', $attendanceForm->course_id);
            }

            // Ajouter un filtre pour le groupe si l'attendanceForm a un group_id
            if ($attendanceForm->group_id) {
                $query->where('group_id', $attendanceForm->group_id);
            }

            // Récupérer les étudiants qui correspondent à ces critères
            $students = $query->get();

            foreach ($students as $student) {
                // Chercher toutes les signatures de l'étudiant pour cet événement
                $signatures = $student->signatures()->where('attendance_form_id', $attendanceForm->id)->get();

                // Ajouter l'étudiant et son statut de signature
                $student->signature_status = $signatures->isNotEmpty() ? 'Signé' : 'Absent';
                $student->signatures_list = $signatures; // Si tu veux utiliser toutes les signatures
            }


            // Mettre à jour la liste des étudiants
            $this->students = $students;
        }
    }

    // Méthode pour écouter les mises à jour en temps réel des signatures
    protected $listeners = ['signatureUpdated' => 'loadStudents'];

    public function render()
    {
        return view('livewire.attendance-event', [
            'students' => $this->students,
        ]);
    }
}

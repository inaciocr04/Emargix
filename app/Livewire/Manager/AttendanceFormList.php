<?php

namespace App\Livewire\Manager;

use App\Models\AttendanceForm;
use Livewire\Component;

class AttendanceFormList extends Component
{

    public $status = 'en_cours'; // Valeur par défaut : "en_cours"
    public $attendanceForms;

    public function mount()
    {
        $this->loadAttendanceForms();
    }

    public function loadAttendanceForms()
    {
        // Filtrer les formulaires d'émargement en fonction du statut
        if ($this->status == 'en_cours') {
            $this->attendanceForms = AttendanceForm::whereNull('signature_teacher')->get();
        } else {
            $this->attendanceForms = AttendanceForm::whereNotNull('signature_teacher')->get();
        }
    }

    public function setStatus($status)
    {
        $this->status = $status;
        $this->loadAttendanceForms();
    }

    public function signatureAdded($attendanceFormId)
    {
        // Mettre à jour la liste des formulaires d'émargement après une signature
        $this->attendanceForms = AttendanceForm::whereNull('signature_teacher')->get();
        // Cette fonction peut aussi être personnalisée pour recharger uniquement un élément spécifique
    }

    public function render()
    {
        return view('livewire.manager.attendance-form-list');
    }
}

<?php

namespace App\Livewire;

use App\Models\AttendanceForm;
use Livewire\Component;

class AttendanceAccess extends Component
{
    public $attendanceForm;

    public function mount($eventId)
    {
        // Charger le formulaire de présence avec l'ID de l'événement
        $this->attendanceForm = AttendanceForm::where('event_id', $eventId)->first();
    }

    public function toggleAccess()
    {
        // Inverser la valeur de la colonne access (0 => 1, 1 => 0)
        $this->attendanceForm->access = !$this->attendanceForm->access;
        $this->attendanceForm->save();
    }

    public function render()
    {
        return view('livewire.attendance-access');
    }
}


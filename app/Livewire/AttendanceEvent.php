<?php

namespace App\Livewire;

use App\Models\AttendanceForm;
use App\Models\StudentSignature;
use Livewire\Component;

class AttendanceEvent extends Component
{
    public $eventId;
    public $signatures = [];

    // Méthode de montage pour initialiser les données
    public function mount($eventId)
    {
        $this->eventId = $eventId;
        $this->loadSignatures();
    }

    // Charger les signatures
    public function loadSignatures()
    {
        $attendanceForm = AttendanceForm::where('event_id', $this->eventId)->latest()->first();

        if ($attendanceForm) {
            $this->signatures = StudentSignature::where('attendance_form_id', $attendanceForm->id)->orderBy('created_at', 'desc')->get();
        }
    }

    // Méthode pour écouter en temps réel les nouvelles signatures
    protected $listeners = ['signatureUpdated' => 'loadSignatures'];

    public function render()
    {
        return view('livewire.attendance-event');
    }
}

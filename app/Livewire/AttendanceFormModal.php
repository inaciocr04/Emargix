<?php

namespace App\Livewire;

use App\Http\Controllers\ApiController;
use App\Models\Teacher;
use App\Models\TpGroup;
use App\Models\Training;
use App\Models\Course;
use App\Models\TdGroup;
use App\Models\AttendanceForm;
use Illuminate\Support\Str;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceFormModal extends Component
{
    public $showModal = false; // Garder cette ligne seule
    public $eventId;
    public $trainings = [];
    public $courses = [];
    public $tdgroups = [];
    public $tpgroups = [];
    public $selectedTraining;
    public $selectedCourse;
    public $selectedTdGroup;
    public $selectedTpGroup;
    public $qrCodeImage = null;

    protected $rules = [
        'selectedTraining' => 'required',
        'selectedTdGroup' => 'nullable',
        'selectedTpGroup' => 'nullable',
        'selectedCourse' => 'nullable',
    ];

    public function mount($eventId)
    {
        // Assigner l'eventId à la propriété publique
        $this->eventId = $eventId;

        // Charger les formations et les groupes
        $this->trainings = Training::all();
        $this->courses = Course::all();
        $this->tdgroups = TdGroup::all();
        $this->tpgroups = TpGroup::all();

        $attendanceForm = AttendanceForm::where('event_id', $this->eventId)->first();
        if ($attendanceForm) {
            $this->selectedTraining = $attendanceForm->training_id;
            $this->selectedCourse = $attendanceForm->course_id;
            $this->selectedTdGroup = $attendanceForm->td_group_id;
            $this->selectedTpGroup = $attendanceForm->tp_group_id;
        }
    }

    // Fonction pour générer le formulaire d'émargement
    public function generateAttendanceForm()
    {
        // Créer une instance de ApiController
        $apiController = new ApiController();

        // Récupérer le sessionId
        $sessionId = $apiController->connect();
        $apiController->setProject(7);

        // Appeler la méthode PlanningProf pour récupérer les événements
        $event = $apiController->getPlanningProf($sessionId);

        // Vérifier si l'événement existe dans les événements récupérés
        $selectedEvent = collect($event)->firstWhere('id', $this->eventId);

        if (!$selectedEvent) {
            return redirect()->back()->with('error', 'Événement introuvable.');
        }

        $user = Auth::user();
        if (!$user) {
            abort(403, 'Utilisateur non connecté.');
        }

        // Récupérer le professeur associé à l'utilisateur
        $teacher = Teacher::where('user_id', $user->id)->first();
        if (!$teacher || !$teacher->professor_id) {
            abort(404, 'Aucun identifiant de professeur trouvé pour cet utilisateur.');
        }

        $professorId = $teacher->id;

        // Vérifier si un formulaire d'émargement pour cet événement existe déjà
        $attendanceForm = AttendanceForm::where('event_id', $selectedEvent['id'])->first();

        // Si aucun formulaire d'émargement n'existe, en créer un nouveau
        if (!$attendanceForm) {
            $attendanceForm = new AttendanceForm();
            $attendanceForm->event_id = $selectedEvent['id'];
            $attendanceForm->event_name = $selectedEvent['name'];
            $attendanceForm->teacher_id = $professorId;
            $attendanceForm->event_date = $selectedEvent['date'];
            $attendanceForm->event_start_hour = $selectedEvent['startHour'];
            $attendanceForm->event_end_hour = $selectedEvent['endHour'];
        }

        // Ajouter le training_id, course_id, group_id si sélectionnés
        $attendanceForm->training_id = $this->selectedTraining;
        $attendanceForm->course_id = $this->selectedCourse ?: null;
        $attendanceForm->td_group_id = $this->selectedTdGroup ?: null;
        $attendanceForm->tp_group_id = $this->selectedTpGroup ?: null;

        // Générer un nouveau token unique
        $attendanceForm->token = Str::uuid()->toString();
        $attendanceForm->save();

        $this->showModal = false;

        return redirect()->route('attendance.generateQrCode', ['eventId' => $selectedEvent['id']]);

    }


    public function render()
    {
        return view('livewire.attendance-form-modal');
    }
}

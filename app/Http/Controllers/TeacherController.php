<?php

namespace App\Http\Controllers;

use App\Models\AttendanceForm;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TeacherController extends Controller
{

    public function showPlanning()
    {
        // Créer une instance de ApiController
        $apiController = new ApiController();

        // Récupérer le sessionId
        $sessionId = $apiController->connect();
        $apiController->setProject(7);
        // Appeler la méthode PlanningProf pour récupérer les événements
        $events = $apiController->getPlanningProf($sessionId);
        // Vérifier si on a des événements
        if (empty($events)) {
            // Aucun événement trouvé, on peut envoyer un tableau vide
            $events = [];
        }

        // Convertisseur des jours de la semaines
        $dayMapping = [
            '0' => 'Lundi',
            '1' => 'Mardi',
            '2' => 'Mercredi',
            '3' => 'Jeudi',
            '4' => 'Vendredi',
            '5' => 'Samedi',
            '6' => 'Dimanche',
        ];

        $groupedEvents = [];
        foreach ($events as $event) {
            $day = $dayMapping[$event['day']] ?? 'Jour inconnu';
            $groupedEvents[$day][] = $event;
        }
        // Passer les événements à la vue
        return view('teacher.planning', compact('groupedEvents', 'dayMapping'));
    }

    public function generateQrCode(Request $request, $eventId)
    {
        // Créer une instance de ApiController
        $apiController = new ApiController();

        // Récupérer le sessionId
        $sessionId = $apiController->connect();
        $apiController->setProject(7);

        // Appeler la méthode PlanningProf pour récupérer les événements
        $event = $apiController->getPlanningProf($sessionId);

        // Vérifier si l'événement existe dans les événements récupérés
        $selectedEvent = collect($event)->firstWhere('id', $eventId);

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
            $attendanceForm->event_id = $selectedEvent['id'];  // Utilisation de l'ID de l'événement spécifique
            $attendanceForm->event_name = $selectedEvent['name'];  // Utilisation de l'ID de l'événement spécifique
            $attendanceForm->teacher_id = $professorId;
            $attendanceForm->event_date = $selectedEvent['date'];  // Date actuelle (lorsque le QR code est généré)
            $attendanceForm->save();
        }

        // Générer l'URL du formulaire d'émargement
        $url = route('studentSignature.create', ['attendanceForm' => $attendanceForm->id]);

        // Générer le QR code contenant l'URL du formulaire d'émargement
        $qrCodeImage = QrCode::size(250)->generate($url);  // Génère le QR code en une image.

        // Retourner la vue avec le QR code généré, l'événement et l'ID de l'événement
        return view('attendance.qr_code', [
            'attendanceForm' => $attendanceForm,
            'qrCodeImage' => $qrCodeImage,
            'eventId' => $eventId,  // Passer l'eventId à la vue
        ]);
    }

    public function getAttendanceFormsByTeacher()
    {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Utilisateur non connecté.');
        }

        // Récupérer le professeur associé à cet utilisateur
        $teacher = Teacher::where('user_id', $user->id)->first();
        if (!$teacher) {
            abort(404, 'Aucun professeur trouvé pour cet utilisateur.');
        }

        // Récupérer tous les formulaires d’émargement créés par ce professeur, triés du plus récent au moins récent
        $attendanceForms = $teacher->attendanceForms()
            ->orderBy('created_at', 'desc') // Ordre décroissant (plus récent d'abord)
            ->get();

        // Retourner une vue ou une réponse JSON
        return view('teacher.attendance-list', compact('attendanceForms'));
    }




    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        //
    }
}

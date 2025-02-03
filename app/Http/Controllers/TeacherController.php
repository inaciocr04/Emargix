<?php

namespace App\Http\Controllers;

use App\Models\AttendanceForm;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TeacherController extends Controller
{

    public function showPlanning()
    {
        $apiController = new ApiController();

        $sessionId = $apiController->connect();
        $apiController->setProject(7);
        $events = $apiController->getPlanningProf($sessionId);

        if (empty($events)) {
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

        // Vérifier si un formulaire d'émargement pour cet événement existe déjà
        $attendanceForm = AttendanceForm::where('event_id', $selectedEvent['id'])->first();

        // Générer un nouveau token
        $attendanceForm->token = Str::uuid()->toString();
        $attendanceForm->save();

        // Générer l'URL du formulaire avec un paramètre unique
        $url = route('studentSignature.create', [
            'eventId' => $attendanceForm->event_id,
            'token' => $attendanceForm->token,
        ]);

        // Générer le QR code avec le nouveau token
        $qrCodeImage = QrCode::size(250)->generate($url);

        // Retourner la vue avec le QR code mis à jour
        return view('attendance.qr_code', [
            'attendanceForm' => $attendanceForm,
            'qrCodeImage' => $qrCodeImage,
            'eventId' => $eventId,
        ]);
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

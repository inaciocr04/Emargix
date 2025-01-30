<?php

namespace App\Http\Controllers;

use App\Models\AttendanceForm;
use App\Models\StudentSignature;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceFormController extends Controller
{
    public function showAttendanceList($eventId)
    {
        // Récupérer le dernier formulaire d'émargement pour l'événement donné
        $attendanceForm = AttendanceForm::where('event_id', $eventId)->latest()->first();

        if (!$attendanceForm) {
            return redirect()->route('teacher.planning')->with('error', 'Événement non trouvé.');
        }


        // Passer les signatures à la vue
        return view('attendance.attendance-event', compact('eventId', 'attendanceForm'));
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
        return view('attendance.attendance-list', compact('attendanceForms'));
    }

    public function getAttendanceFormsManager()
    {
        $attendanceForms = AttendanceForm::all();

        return view('manager.attendance-list', [
            'attendanceForms' => $attendanceForms,
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
    public function show(AttendanceForm $attendanceForm)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttendanceForm $attendanceForm)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AttendanceForm $attendanceForm)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttendanceForm $attendanceForm)
    {
        //
    }
}

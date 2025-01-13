<?php

namespace App\Http\Controllers;

use App\Models\AttendanceForm;
use App\Models\StudentSignature;
use Illuminate\Http\Request;
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

        // Récupérer toutes les signatures des étudiants associées à ce formulaire d'émargement
        // Supposons que la table `student_signature` a un champ `attendance_form_id` pour lier les signatures à un formulaire d'émargement
        $signatures = StudentSignature::where('attendance_form_id', $attendanceForm->id)->get();

        // Passer les signatures à la vue
        return view('teacher.attendance-event', compact('eventId', 'attendanceForm'));
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

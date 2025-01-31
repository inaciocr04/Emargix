<?php

namespace App\Http\Controllers;

use App\Models\AttendanceForm;
use App\Models\Student;
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

        // Créer une requête pour récupérer les étudiants en fonction du formulaire d'émargement
        $query = Student::where('training_id', $attendanceForm->training_id);

        // Ajouter un filtre pour le cours si l'attendanceForm a un course_id
        if ($attendanceForm->course_id) {
            $query->where('course_id', $attendanceForm->course_id);
        }

        // Ajouter un filtre pour le groupe TP si l'attendanceForm a un tp_group_id
        if ($attendanceForm->tp_group_id) {
            $query->where('tp_group_id', $attendanceForm->tp_group_id);
        }

        // Ajouter un filtre pour le groupe TD si l'attendanceForm a un td_group_id
        if ($attendanceForm->td_group_id) {
            $query->where('td_group_id', $attendanceForm->td_group_id);
        }

        // Exécuter la requête pour récupérer les étudiants filtrés
        $students = $query->get();

        // Récupérer toutes les signatures des étudiants pour ce formulaire d'émargement
        $signedStudents = StudentSignature::where('attendance_form_id', $attendanceForm->id)
            ->pluck('student_id')
            ->toArray();

        // Initialiser les compteurs
        $presentCount = 0;
        $absentCount = 0;

        // Parcourir les étudiants filtrés pour déterminer les présents et absents
        foreach ($students as $student) {
            if (in_array($student->id, $signedStudents)) {
                $presentCount++;
            } else {
                $absentCount++;
            }
        }

        // Passer les résultats à la vue
        return view('attendance.attendance-event', compact('eventId', 'attendanceForm', 'presentCount', 'absentCount'));
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

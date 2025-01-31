<?php

namespace App\Http\Controllers;

use App\Models\AttendanceForm;
use App\Models\Student;
use App\Models\StudentSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class StudentSignatureController extends Controller
{
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
    public function create(Request $request, $eventId)
    {
        // Vérifier si l'événement existe
        $attendanceForm = AttendanceForm::where('event_id', $eventId)->firstOrFail();

        // Vérifier si le token est valide
        if ($attendanceForm->token !== $request->query('token')) {
            // Rediriger avec un message d'erreur si le token ne correspond pas
            return redirect()->route('qr.scan')->with('error', 'L\'émargement n\'est pas possible. Token invalide.');
        }

        // Vérifier si l'utilisateur est connecté
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Utilisateur non connecté.');
        }

        // Récupérer l'étudiant associé à l'utilisateur
        $student = Student::where('user_id', $user->id)->first();
        if (!$student) {
            abort(404, 'Aucun identifiant d\'étudiant trouvé pour cet utilisateur.');
        }

        $studentId = $student->id;

        // Vérifier si l'étudiant a déjà signé pour cet événement
        $existingSignature = StudentSignature::where('student_id', $studentId)
            ->where('attendance_form_id', $attendanceForm->id)
            ->first();

        if ($existingSignature) {
            return redirect()->route('dashboard')->with('error', 'Vous avez déjà signé pour cet événement.');
        }

        // Retourner la vue avec les données nécessaires
        return view('studentSignature.create', [
            'studentId' => $studentId,
            'attendanceForm' => $attendanceForm,
            'eventId' => $eventId,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request, $studentId, $eventId)
    {
        // Trouver le formulaire de présence en utilisant event_id
        $attendanceForm = AttendanceForm::where('event_id', $eventId)->first();

        if (!$attendanceForm) {
            return redirect()->back()->with('error', 'Formulaire introuvable.');
        }

        if ($attendanceForm->signature_teacher) {
            return redirect()->route('dashboard')->with('error', 'Vous ne pouvez plus signer ce formulaire car le professeur a déjà signé.');
        }

        // Vérifier si l'étudiant a déjà signé
        $existingSignature = StudentSignature::where('student_id', $studentId)
            ->where('attendance_form_id', $attendanceForm->id)
            ->first();

        if ($existingSignature) {
            return redirect()->route('dashboard')->with('error', 'Vous avez déjà signé pour cet événement.');
        }

        // Décoder la signature
        $signatureBase64 = $request->input('signature');
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureBase64));

        $user = Auth::user();
        $userFolder = 'signatures/students/' . $user->id;
        $filename = uniqid() . '.png';

        $path = public_path('storage/' . $userFolder);
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        file_put_contents($path . '/' . $filename, $imageData);

        // Enregistrer la signature
        $signature = new StudentSignature();
        $signature->student_id = $studentId;
        $signature->attendance_form_id = $attendanceForm->id;
        $signature->signature = 'storage/' . $userFolder . '/' . $filename;
        $signature->save();

        return redirect()->route('dashboard')->with('success', 'Présence enregistrée avec succès !');
    }




    /**
     * Display the specified resource.
     */
    public function show(StudentSignature $studentSignature)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentSignature $studentSignature)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentSignature $studentSignature)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentSignature $studentSignature)
    {
        //
    }
}

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
    public function create($attendanceFormId)
    {
        $attendanceForm = AttendanceForm::findOrFail($attendanceFormId);


        $user = Auth::user();
        if (!$user) {
            abort(403, 'Utilisateur non connecté.');
        }

        // Récupérer le professeur associé à l'utilisateur
        $student = Student::where('user_id', $user->id)->first();

        if (!$student || !$student->id) {
            abort(404, 'Aucun identifiant de professeur trouvé pour cet utilisateur.');
        }

        $studentId = $student->id;

        // Vérifier si l'étudiant a déjà signé pour cet événement
        $existingSignature = StudentSignature::where('student_id', $studentId)
            ->where('attendance_form_id', $attendanceFormId)
            ->first();

        // Si l'étudiant a déjà signé, bloquer l'accès et rediriger vers le dashboard
        if ($existingSignature) {
            return redirect()->route('dashboard')->with('error', 'Vous avez déjà signé pour cet événement.');
        }

        return view('studentSignature.create', [
            'studentId' => $studentId,
            'attendanceForm' => $attendanceForm,
            'attendanceFormId' => $attendanceFormId
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request, $studentId, $attendanceFormId)
    {
        $attendanceForm = AttendanceForm::find($attendanceFormId);

        if (!$attendanceForm) {
            return redirect()->back()->with('error', 'Formulaire introuvable.');
        }

        if ($attendanceForm->signature_teacher) {
            return redirect()->route('dashboard')->with('error', 'Vous ne pouvez plus signer ce formulaire car le professeur a déjà signé.');
        }

        // Vérifier si l'étudiant a déjà signé pour ce formulaire de présence
        $existingSignature = StudentSignature::where('student_id', $studentId)
            ->where('attendance_form_id', $attendanceFormId)
            ->first();

        if ($existingSignature) {
            return redirect()->route('dashboard')->with('error', 'Vous avez déjà signé pour cet événement.');
        }

        // Récupérer la signature sous forme base64
        $signatureBase64 = $request->input('signature');
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureBase64));

        // Récupérer l'utilisateur connecté
        $user = Auth::user(); // Récupère l'utilisateur connecté

        // Créer un dossier spécifique pour l'utilisateur connecté dans public/storage
        $userFolder = 'signatures/' . $user->id; // Utilisation de l'ID de l'utilisateur pour créer un dossier unique

        // Générer un nom de fichier unique pour chaque signature
        $filename = uniqid() . '.png'; // Utilisation de uniqid pour générer un nom de fichier unique

        // Créer le dossier si nécessaire (stocké dans public/storage)
        $path = public_path('storage/' . $userFolder);
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true); // Crée le dossier si il n'existe pas
        }

        // Sauvegarder l'image dans le dossier de l'utilisateur
        file_put_contents($path . '/' . $filename, $imageData);

        // Enregistrer la signature dans la base de données
        $signature = new StudentSignature();
        $signature->student_id = $studentId;
        $signature->attendance_form_id = $attendanceFormId;
        $signature->signature = 'storage/' . $userFolder . '/' . $filename; // Chemin relatif pour l'accès public
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

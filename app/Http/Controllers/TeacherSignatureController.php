<?php

namespace App\Http\Controllers;

use App\Models\AttendanceForm;
use App\Models\TeacherSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class TeacherSignatureController extends Controller
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $eventId)
    {
        $request->validate([
            'signature' => 'required',
        ]);

        // Vérifier si le professeur est connecté
        $user = Auth::user();
        if (!$user || !$user->isTeacher()) { // Assurez-vous que la méthode `isTeacher` existe
            abort(403, 'Seuls les professeurs peuvent signer.');
        }

        // Récupérer l'événement
        $attendanceForm = AttendanceForm::where('event_id', $eventId)->latest()->first();

        if (!$attendanceForm) {
            return redirect()->back()->with('error', 'Événement introuvable.');
        }

        // Récupérer la signature sous forme base64
        $signatureBase64 = $request->input('signature');
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureBase64));

        // Créer un dossier spécifique pour l'événement et le professeur
        $userFolder = 'signatures/teachers/' . $user->id . '/' . $eventId; // Chemin spécifique pour le professeur et l'événement
        $path = public_path('storage/' . $userFolder);

        // Créer le dossier s'il n'existe pas
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        // Générer un nom de fichier unique
        $filename = uniqid() . '.png';
        file_put_contents($path . '/' . $filename, $imageData);

        // Sauvegarder dans la base de données (si vous avez une table dédiée)
        $attendanceForm->signature_teacher = 'storage/' . $userFolder . '/' . $filename;
        $attendanceForm->save();

        return redirect()->back()->with('success', 'Signature enregistrée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TeacherSignature $teacherSignature)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TeacherSignature $teacherSignature)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TeacherSignature $teacherSignature)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TeacherSignature $teacherSignature)
    {
        //
    }
}

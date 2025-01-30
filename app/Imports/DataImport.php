<?php

namespace App\Imports;

use App\Models\Course;
use App\Models\Group;
use App\Models\Student;
use App\Models\Training;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DataImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $training = Training::updateOrCreate(
            ['name' => $row['formation'] ?? 'Formation inconnue']
        );

        $course = Course::updateOrCreate(
            ['name' => $row['parcours'] ?? 'Parcours inconnu']
        );

        $group = Group::updateOrCreate(
            ['name' => $row['groupe'] ?? 'Groupe inconnu']
        );

        try {
            $student = Student::updateOrCreate(
                ['email' => $row['courriel_unistra']],
                [
                    'firstname' => $row['prenom'],
                    'lastname' => $row['nom'],
                    'student_statu' => $row['statut'] ?? 'Inconnu',
                    'training_id' => $training->id,
                    'course_id' => $course->id,
                    'group_id' => $group->id,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'importation de l\'étudiant : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur d\'importation : ' . $e->getMessage());
        }


        return $student;
    }
}

<?php

namespace App\Imports;

use App\Models\Course;
use App\Models\TdGroup;
use App\Models\Student;
use App\Models\TpGroup;
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

        $tdgroup = TdGroup::updateOrCreate(
            ['name' => $row['groupe td'] ?? 'Groupe inconnu']
        );
        $tpgroup = TpGroup::updateOrCreate(
            ['name' => $row['groupe tp'] ?? 'Groupe inconnu']
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
                    'td_group_id' => $tdgroup->id,
                    'tp_group_id' => $tpgroup->id,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'importation de l\'étudiant : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur d\'importation : ' . $e->getMessage());
        }


        return $student;
    }
}

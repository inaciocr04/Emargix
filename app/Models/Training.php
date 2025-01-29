<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    /** @use HasFactory<\Database\Factories\TrainingFactory> */
    use HasFactory;

    public function attendanceForms()
    {
        return $this->hasMany(AttendanceForm::class, 'training_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'training_id');
    }
}

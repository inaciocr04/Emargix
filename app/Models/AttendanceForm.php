<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceForm extends Model
{
    use HasFactory;

    protected $table = 'attendance_forms';

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function studentSignatures()
    {
        return $this->hasMany(StudentSignature::class);
    }

    public function teacherSignatures()
    {
        return $this->hasMany(TeacherSignature::class);
    }
}

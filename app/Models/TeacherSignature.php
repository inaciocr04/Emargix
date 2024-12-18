<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherSignature extends Model
{
    use HasFactory;

    protected $table = 'teacher_signatures';

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function attendanceForm()
    {
        return $this->belongsTo(AttendanceForm::class);
    }
}

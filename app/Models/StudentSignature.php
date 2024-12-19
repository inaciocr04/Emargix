<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSignature extends Model
{
    use HasFactory;

    protected $table = 'student_signatures';

    protected $fillable = [
        'student_id',
        'attendance_form_id',
        'signature',
        'signature_date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function attendanceForm()
    {
        return $this->belongsTo(AttendanceForm::class);
    }
}

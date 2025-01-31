<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceForm extends Model
{
    use HasFactory;

    protected $table = 'attendance_forms';

    protected $fillable = [
        'event_id',
        'event_name',
        'event_date',
        'form_unique_code',
    ];
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function student()
    {
        return $this->HasMany(Student::class);
    }

    public function studentSignatures()
    {
        return $this->hasMany(StudentSignature::class);
    }

    public function teacherSignatures()
    {
        return $this->hasMany(TeacherSignature::class);
    }

    public function training()
    {
        return $this->belongsTo(Training::class, 'training_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function td_group()
    {
        return $this->belongsTo(TdGroup::class, 'td_group_id');
    }
    public function tp_group()
    {
        return $this->belongsTo(TpGroup::class, 'tp_group_id');
    }
}

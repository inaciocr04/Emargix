<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];
    public function attendanceForms()
    {
        return $this->hasMany(AttendanceForm::class, 'training_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'td_group_id');
    }
}

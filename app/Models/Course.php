<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TdGroup extends Model
{
    public function attendanceForms()
    {
        return $this->hasMany(AttendanceForm::class, 'training_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'td_group_id');
    }
}

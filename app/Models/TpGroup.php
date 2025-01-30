<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TpGroup extends Model
{
    /** @use HasFactory<\Database\Factories\GroupFactory> */
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
        return $this->hasMany(Student::class, 'tp_group_id');
    }
}

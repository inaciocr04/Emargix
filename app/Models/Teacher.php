<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teacher extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherFactory> */
    use HasFactory;

    protected $table = 'teachers';

    protected $fillable = [
        'name',
        'email',
        'professor_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendanceForms()
    {
        return $this->hasMany(AttendanceForm::class);
    }

    public function signatures()
    {
        return $this->hasMany(TeacherSignature::class);
    }
}

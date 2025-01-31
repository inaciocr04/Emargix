<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;

    protected $fillable = [
        'lastname',
        'firstname',
        'email',
        'student_statu',
        'training_id',
        'course_id',
        'td_group_id',
        'tp_group_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function signatures()
    {
        return $this->hasMany(StudentSignature::class);
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

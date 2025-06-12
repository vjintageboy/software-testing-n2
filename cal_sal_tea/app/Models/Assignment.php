<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'course_class_id',
        'notes',
    ];

    /**
     * Get the teacher for this assignment.
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the class for this assignment.
     */
    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class, 'course_class_id');
    }
}

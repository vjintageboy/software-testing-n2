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
    ];

    /**
     * Lấy về giáo viên của phân công này.
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Lấy về lớp học phần của phân công này.
     */
    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class);
    }
}

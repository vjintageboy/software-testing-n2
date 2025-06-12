<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseClass extends Model
{
    use HasFactory;
    
    protected $table = 'course_classes';

    protected $fillable = [
        'class_code',
        'course_id',
        'term_id',
        'number_of_students',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * Get the assignment for the class.
     * Vì mỗi lớp chỉ được phân công 1 lần (unique constraint), nên đây là quan hệ hasOne.
     */
    public function assignment()
    {
        return $this->hasOne(Assignment::class, 'course_class_id');
    }
}

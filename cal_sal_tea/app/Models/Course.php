<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'course_code',
        'credits',
        'faculty_id', // Thêm faculty_id vào fillable
    ];

    /**
     * Lấy khoa mà học phần này thuộc về.
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the course classes for the course.
     */
    public function courseClasses()
    {
        return $this->hasMany(CourseClass::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'teacher_code',
        'full_name',
        'date_of_birth',
        'phone',
        'email',
        'faculty_id',
        'degree_id',
        'is_active',
    ];

    /**
     * Get the faculty that the teacher belongs to.
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the degree of the teacher.
     */
    public function degree()
    {
        return $this->belongsTo(Degree::class);
    }
    
    /**
     * Get the assignments for the teacher.
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Get the payrolls for the teacher.
     */
    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }
}

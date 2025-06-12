<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'term_id',
        'assignment_id',
        'calculation_date',
        'total_amount',
        'base_pay_snapshot',
        'degree_coeff_snapshot',
        'course_coeff_snapshot',
        'class_coeff_snapshot',
        'standard_periods_snapshot',
        'number_of_students_snapshot',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'calculation_date' => 'datetime', // Thuộc tính gốc của model
        'last_calculated' => 'datetime', // Thuộc tính từ aggregate function cần cast
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }
}

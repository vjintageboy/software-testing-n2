<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ClassSizeCoefficient;

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
    /**
 * Lấy hệ số theo sĩ số của lớp học phần.
 *
 * @return float|string
 */
    public function getCssCoefficientAttribute()
{
    $coefficient = ClassSizeCoefficient::where('min_students', '<=', $this->number_of_students)
                                       ->where('max_students', '>=', $this->number_of_students)
                                       ->first();

    return $coefficient ? $coefficient->coefficient : 'Chưa có';
}
/**
     * (HÀM MỚI) Lấy tên giảng viên được phân công cho lớp học.
     * Giúp code trong view gọn hơn.
     *
     * @return string
     */
    public function getTeacherNameAttribute(): string
    {
        $this->loadMissing('assignment.teacher');
        // Sử dụng toán tử null coalescing để đảm bảo luôn trả về một chuỗi.
        // Đồng thời, truy cập thuộc tính 'full_name' như đã định nghĩa trong model Teacher.
        return $this->assignment?->teacher?->full_name ?? 'Chưa phân công';
    }
}

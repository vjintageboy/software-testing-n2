<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Term extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'academic_year',
        'start_date',
        'end_date',
    ];

    /**
     * Lấy kỳ học đang hoạt động (active term).
     * Kỳ học được coi là active nếu ngày hiện tại nằm trong khoảng start_date và end_date.
     * Nếu có nhiều kỳ active (trường hợp hiếm), sẽ lấy kỳ có end_date gần nhất.
     *
     * @return self|null
     */
    public static function getActiveTerm(): ?self
    {
        return self::where('start_date', '<=', Carbon::today())
                    ->where('end_date', '>=', Carbon::today())
                    ->orderBy('end_date', 'asc')->first();
    }
}

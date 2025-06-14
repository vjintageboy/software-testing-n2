<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Degree extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'abbreviation', // Thêm trường mới vào đây
        'coefficient',
    ];

    /**
     * Get the teachers for this degree.
     */
    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }
}

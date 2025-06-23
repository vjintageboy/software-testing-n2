<?php

// app/Models/Faculty.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'abbreviation',
        'description',
    ];

    /**
     * Get the teachers for this faculty.
     */
    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    /**
     * Get the courses for this faculty.
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}

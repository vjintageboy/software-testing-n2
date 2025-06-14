<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSizeCoefficient extends Model
{
    use HasFactory;

    protected $fillable = [
        'min_students',
        'max_students',
        'coefficient',
        'valid_from',
        'valid_to',
        'description',
    ];
}

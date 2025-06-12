<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'base_pay_per_period',
        'effective_date',
        'description',
    ];
}

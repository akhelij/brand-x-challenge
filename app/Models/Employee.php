<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date_of_birth' => 'date',
        'time_of_birth' => 'datetime',
        'date_of_joining' => 'date',
        'age_in_years' => 'decimal:2',
        'age_in_company_years' => 'decimal:2'
    ];
}

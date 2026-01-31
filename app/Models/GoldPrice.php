<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoldPrice extends Model
{
    protected $fillable = ['daily_price', 'date'];

    protected $casts = [
        'date' => 'date',
    ];
}

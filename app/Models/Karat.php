<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karat extends Model
{
    protected $fillable = ['karat_value', 'multiplier', 'description'];
}

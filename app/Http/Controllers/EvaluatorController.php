<?php

namespace App\Http\Controllers;

use App\Models\GoldPrice;
use App\Models\Karat;

class EvaluatorController extends Controller
{
    public function index()
    {
        $karats = Karat::orderBy('karat_value', 'desc')->get();
        $todayGoldPrice = GoldPrice::whereDate('date', today())->first();

        return view('evaluator.index', compact('karats', 'todayGoldPrice'));
    }
}


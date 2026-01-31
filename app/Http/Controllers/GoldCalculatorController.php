<?php

namespace App\Http\Controllers;

use App\Models\GoldPrice;
use App\Models\Karat;
use App\Services\ReceiptValueRecalculationService;
use Illuminate\Http\Request;

class GoldCalculatorController extends Controller
{
    public function index()
    {
        $todayPrice = GoldPrice::whereDate('date', today())->first();
        $karats = Karat::orderBy('karat_value', 'desc')->get();

        return view('calculator.index', [
            'todayPrice' => $todayPrice,
            'karats' => $karats,
        ]);
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'grams' => 'required|numeric|min:0.01',
            'karat_id' => 'required|exists:karats,id',
        ]);

        $todayPrice = GoldPrice::whereDate('date', today())->first();

        if (!$todayPrice) {
            return back()->withErrors(['error' => 'Daily gold price not set']);
        }

        $karat = Karat::find($validated['karat_id']);
        $pricePerGram = $todayPrice->daily_price * $karat->multiplier;
        $totalPrice = $pricePerGram * $validated['grams'];

        // Calculate buying price offers with margins
        $offers = [
            [
                'margin' => 100,
                'price_per_gram' => round($pricePerGram - 100, 2),
                'total_price' => round(($pricePerGram - 100) * $validated['grams'], 2),
                'profit_per_gram' => round(100, 2),
                'total_profit' => round(100 * $validated['grams'], 2),
            ],
            [
                'margin' => 200,
                'price_per_gram' => round($pricePerGram - 200, 2),
                'total_price' => round(($pricePerGram - 200) * $validated['grams'], 2),
                'profit_per_gram' => round(200, 2),
                'total_profit' => round(200 * $validated['grams'], 2),
            ],
            [
                'margin' => 300,
                'price_per_gram' => round($pricePerGram - 300, 2),
                'total_price' => round(($pricePerGram - 300) * $validated['grams'], 2),
                'profit_per_gram' => round(300, 2),
                'total_profit' => round(300 * $validated['grams'], 2),
            ],
            [
                'margin' => 400,
                'price_per_gram' => round($pricePerGram - 400, 2),
                'total_price' => round(($pricePerGram - 400) * $validated['grams'], 2),
                'profit_per_gram' => round(400, 2),
                'total_profit' => round(400 * $validated['grams'], 2),
            ],
        ];

        return back()->with([
            'pricePerGram' => round($pricePerGram, 2),
            'totalPrice' => round($totalPrice, 2),
            'karatValue' => $karat->karat_value,
            'grams' => $validated['grams'],
            'offers' => $offers,
        ]);
    }

    public function admin()
    {
        $todayPrice = GoldPrice::whereDate('date', today())->first();
        return view('calculator.admin', ['todayPrice' => $todayPrice]);
    }

    public function storePrice(Request $request)
    {
        $validated = $request->validate([
            'daily_price' => 'required|numeric|min:0.01',
        ]);

        GoldPrice::updateOrCreate(
            ['date' => today()],
            ['daily_price' => $validated['daily_price']]
        );

        // Recalculate total_item_value and profit_margin for all accepted receipts
        $service = new ReceiptValueRecalculationService();
        $updatedCount = $service->recalculateAcceptedReceiptsValues();

        $message = 'Gold price updated successfully';
        if ($updatedCount > 0) {
            $message .= " | Updated {$updatedCount} receipt(s)";
        }

        return redirect('/admin')->with('success', $message);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = [
        'receipt_number',
        'owner_name',
        'owner_contact',
        'pawn_shop_name',
        'address',
        'items',
        'lukat_fee',
        'total_item_value',
        'profit_margin',
        'status',
        'final_buying_price',
        'e_bagsak',
        'e_bagsak_at',
    ];

    protected $casts = [
        'items' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'e_bagsak_at' => 'datetime',
    ];

    /**
     * Calculate total value of all items in receipt
     */
    public function calculateItemsValue(): float
    {
        $totalValue = 0;
        $goldPrice = GoldPrice::whereDate('date', today())->first();

        if (!$goldPrice || !$this->items) {
            return 0;
        }

        foreach ($this->items as $item) {
            $karat = Karat::find($item['karat_id']);
            if ($karat) {
                $pricePerGram = $goldPrice->daily_price * $karat->multiplier;
                $totalValue += $pricePerGram * $item['grams'];
            }
        }

        return $totalValue;
    }

    /**
     * Calculate profit margin (value - lukat fee)
     */
    public function calculateProfitMargin(): float
    {
        $itemsValue = $this->calculateItemsValue();
        $profitMargin = $itemsValue - $this->lukat_fee;
        return max(0, $profitMargin);
    }

    /**
     * Get calculation breakdown for display
     */
    public function getBreakdown(): array
    {
        $goldPrice = GoldPrice::whereDate('date', today())->first();
        $breakdown = [];

        if (!$goldPrice) {
            return $breakdown;
        }

        if ($this->items) {
            foreach ($this->items as $item) {
                $karat = Karat::find($item['karat_id']);
                if ($karat) {
                    $pricePerGram = $goldPrice->daily_price * $karat->multiplier;
                    $itemTotal = $pricePerGram * $item['grams'];

                    $breakdown[] = [
                        'grams' => $item['grams'],
                        'karat' => $karat->karat_value,
                        'price_per_gram' => round($pricePerGram, 2),
                        'total' => round($itemTotal, 2),
                    ];
                }
            }
        }

        return $breakdown;
    }
}



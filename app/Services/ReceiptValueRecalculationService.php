<?php

namespace App\Services;

use App\Models\GoldPrice;
use App\Models\Karat;
use App\Models\Receipt;

class ReceiptValueRecalculationService
{
    /**
     * Recalculate total_item_value and profit_margin for all accepted receipts
     * when gold price changes
     */
    public function recalculateAcceptedReceiptsValues(): int
    {
        $updatedCount = 0;
        $todayGoldPrice = GoldPrice::whereDate('date', today())->first();

        if (!$todayGoldPrice) {
            return $updatedCount;
        }

        // Get all accepted receipts (status = 'completed' and e_bagsak = false)
        $acceptedReceipts = Receipt::where('status', 'completed')
            ->where('e_bagsak', false)
            ->get();

        foreach ($acceptedReceipts as $receipt) {
            $newTotalItemValue = $this->calculateItemsValue($receipt, $todayGoldPrice);
            $newProfitMargin = max(0, $newTotalItemValue - $receipt->lukat_fee);

            // Update the receipt if values have changed
            if ($newTotalItemValue != $receipt->total_item_value || $newProfitMargin != $receipt->profit_margin) {
                $receipt->update([
                    'total_item_value' => $newTotalItemValue,
                    'profit_margin' => $newProfitMargin,
                ]);
                $updatedCount++;
            }
        }

        return $updatedCount;
    }

    /**
     * Calculate the total value of items for a receipt using a specific gold price
     */
    private function calculateItemsValue(Receipt $receipt, GoldPrice $goldPrice): float
    {
        $totalValue = 0;

        if (!$receipt->items) {
            return 0;
        }

        foreach ($receipt->items as $item) {
            $karat = Karat::find($item['karat_id']);
            if ($karat) {
                $pricePerGram = $goldPrice->daily_price * $karat->multiplier;
                $totalValue += $pricePerGram * $item['grams'];
            }
        }

        return $totalValue;
    }
}

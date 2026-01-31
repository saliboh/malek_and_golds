<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Karat;
use App\Models\GoldPrice;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    /**
     * Show receipt creation form
     */
    public function create()
    {
        $karats = Karat::orderBy('karat_value', 'desc')->get();
        return view('receipts.create', compact('karats'));
    }

    /**
     * Store receipt and calculate prices
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receipt_number' => 'required|unique:receipts|string|max:50',
            'owner_name' => 'required|string|max:255',
            'owner_contact' => 'nullable|string|max:20',
            'pawn_shop_name' => 'nullable|string|max:255',
            'items' => 'required|json',
            'lukat_fee' => 'required|numeric|min:0',
        ]);

        // Decode JSON string to array
        $items = json_decode($validated['items'], true);

        // Validate items are not empty
        if (empty($items)) {
            return back()->withErrors(['items' => 'Please add at least one item']);
        }

        // First, create receipt with all the base data
        $receipt = Receipt::create([
            'receipt_number' => $validated['receipt_number'],
            'owner_name' => $validated['owner_name'],
            'owner_contact' => $validated['owner_contact'],
            'pawn_shop_name' => $validated['pawn_shop_name'],
            'items' => $items,
            'lukat_fee' => $validated['lukat_fee'],
            'total_item_value' => 0, // Will be calculated next
            'profit_margin' => 0, // Will be calculated next
        ]);

        // Now calculate the values with the saved receipt
        $totalItemValue = $receipt->calculateItemsValue();
        $profitMargin = $totalItemValue - $receipt->lukat_fee;
        $profitMargin = max(0, $profitMargin); // Don't allow negative

        // Update with calculated values
        $receipt->update([
            'total_item_value' => $totalItemValue,
            'profit_margin' => $profitMargin,
        ]);

        return redirect()->route('receipts.show', $receipt)->with('success', 'Receipt evaluated successfully!');
    }

    /**
     * Show receipt details and evaluation
     */
    public function show(Receipt $receipt)
    {
        $breakdown = $receipt->getBreakdown();
        $karats = Karat::orderBy('karat_value', 'desc')->get();
        return view('receipts.show', compact('receipt', 'breakdown', 'karats'));
    }

    /**
     * List all receipts
     */
    public function index()
    {
        $activeReceipts = Receipt::whereIn('status', ['pending', 'offered'])->orderBy('created_at', 'desc')->paginate(15, ['*'], 'active_page');
        $acceptedReceipts = Receipt::where('status', 'completed')->where('e_bagsak', false)->orderBy('created_at', 'desc')->paginate(15, ['*'], 'accepted_page');
        $bagsaksReceipts = Receipt::where('e_bagsak', true)->orderBy('e_bagsak_at', 'desc')->paginate(15, ['*'], 'bagsak_page');

        // Get today's gold price for projected profit calculation
        $todayGoldPrice = GoldPrice::whereDate('date', today())->first();

        // Calculate projected profit from accepted receipts using CURRENT gold price
        // (not yet received, so we calculate based on today's rate)
        $projectedProfit = 0;
        if ($todayGoldPrice) {
            $acceptedReceipts->getCollection()->each(function($receipt) use ($todayGoldPrice, &$projectedProfit) {
                $currentItemValue = 0;
                foreach ($receipt->items as $item) {
                    $karat = Karat::find($item['karat_id']);
                    if ($karat) {
                        $pricePerGram = $todayGoldPrice->daily_price * $karat->multiplier;
                        $currentItemValue += $pricePerGram * $item['grams'];
                    }
                }
                $profit = max(0, $currentItemValue - $receipt->lukat_fee - $receipt->final_buying_price);
                $projectedProfit += $profit;
            });
        }

        // Calculate actual profit from bagsak receipts (use stored values - already received)
        $actualProfit = Receipt::where('e_bagsak', true)
            ->get()
            ->sum(function($receipt) {
                return max(0, $receipt->total_item_value - $receipt->lukat_fee - $receipt->final_buying_price);
            });

        return view('receipts.index', compact('activeReceipts', 'acceptedReceipts', 'bagsaksReceipts', 'projectedProfit', 'actualProfit'));
    }

    /**
     * Make an offer on receipt
     */
    public function makeOffer(Request $request, Receipt $receipt)
    {
        $validated = $request->validate([
            'final_buying_price' => 'required|numeric|min:0|max:' . $receipt->profit_margin,
        ]);

        $receipt->update([
            'final_buying_price' => $validated['final_buying_price'],
            'status' => 'offered',
        ]);

        return back()->with('success', 'Offer made! Price: ₱' . number_format($validated['final_buying_price'], 0));
    }

    /**
     * Accept offer and complete purchase
     */
    public function accept(Receipt $receipt)
    {
        $receipt->update(['status' => 'completed']);
        return back()->with('success', 'Purchase completed!');
    }

    /**
     * Update receipt with edited items and lukat fee
     */
    public function update(Request $request, Receipt $receipt)
    {
        $validated = $request->validate([
            'items' => 'required|json',
            'lukat_fee' => 'required|numeric|min:0',
        ]);

        // Decode JSON string to array
        $items = json_decode($validated['items'], true);

        // Validate items are not empty
        if (empty($items)) {
            return back()->withErrors(['items' => 'Please add at least one item']);
        }

        // Update receipt
        $receipt->update([
            'items' => $items,
            'lukat_fee' => $validated['lukat_fee'],
        ]);

        // Recalculate values
        $totalItemValue = $receipt->calculateItemsValue();
        $profitMargin = $totalItemValue - $receipt->lukat_fee;
        $profitMargin = max(0, $profitMargin);

        $receipt->update([
            'total_item_value' => $totalItemValue,
            'profit_margin' => $profitMargin,
        ]);

        return back()->with('success', 'Receipt updated successfully!');
    }

    /**
     * Mark receipt as E bagsak (forwarded to boss and sold)
     */
    public function markEBagsak(Receipt $receipt)
    {
        $receipt->update([
            'e_bagsak' => true,
            'e_bagsak_at' => now(),
        ]);

        return back()->with('success', 'Marked as E bagsak! ✅ Gold forwarded to boss.');
    }

    /**
     * Delete a receipt
     */
    public function destroy(Receipt $receipt)
    {
        $receiptNumber = $receipt->receipt_number;
        $receipt->delete();

        return redirect()->route('receipts.index')->with('success', "Receipt {$receiptNumber} deleted successfully!");
    }
}

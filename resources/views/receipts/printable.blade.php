<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Receipt - {{ $receipt->receipt_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 13px; }
        .row { display: grid; grid-template-columns: 110px 1fr; border-bottom: 1px solid #f3f4f6; }
        .row:last-child { border-bottom: none; }
        .lbl { background: #f9fafb; padding: 7px 10px; font-size: 11px; font-weight: 600; color: #9ca3af; display: flex; align-items: flex-start; padding-top: 9px; }
        .val { padding: 7px 10px; display: flex; align-items: center; flex-wrap: wrap; gap: 4px; }
        @media print { .no-print { display: none !important; } body { background: white; } }
    </style>
</head>
<body class="bg-gray-100">

@php
    $currentOffer = $receipt->final_buying_price ?? 0;
    $profitAtOffer = $receipt->total_item_value - $receipt->lukat_fee - $currentOffer;
    $offerRange = $receipt->profit_margin;
    $isNegative = $offerRange < 0;
    $hasNegativeProfit = $profitAtOffer < 0;
@endphp

<div class="max-w-sm mx-auto px-3 pt-3 pb-4 space-y-2">

    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl px-3 py-2.5 flex items-center justify-between shadow">
        <div>
            <p class="text-white font-black text-sm leading-tight">💎 Malek & Golds</p>
            <p class="text-indigo-200 text-xs">#{{ $receipt->receipt_number }} · {{ $receipt->created_at->format('M d, Y') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs bg-white/20 text-white font-semibold px-2 py-0.5 rounded-lg">
                {{ $receipt->source ?? 'Malek & Golds' }}
            </span>
            <button onclick="window.print()" class="no-print text-xs bg-white/20 hover:bg-white/30 text-white font-bold px-2 py-0.5 rounded-lg">🖨️</button>
        </div>
    </div>

    <!-- Owner Info Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
        <div class="row">
            <div class="lbl">Owner</div>
            <div class="val"><span class="text-sm font-black text-gray-900">{{ $receipt->owner_name ?? 'N/A' }}</span></div>
        </div>
        @if($receipt->owner_contact)
        <div class="row">
            <div class="lbl">Contact</div>
            <div class="val"><span class="text-sm font-semibold text-gray-800">{{ $receipt->owner_contact }}</span></div>
        </div>
        @endif
        @if($receipt->pawn_shop_name)
        <div class="row">
            <div class="lbl">Pawn Shop</div>
            <div class="val"><span class="text-sm font-semibold text-gray-800">{{ $receipt->pawn_shop_name }}</span></div>
        </div>
        @endif
        @if($receipt->address)
        <div class="row">
            <div class="lbl">Address</div>
            <div class="val" style="align-items:flex-start;padding-top:9px;"><span class="text-sm font-semibold text-gray-800 leading-snug">{{ $receipt->address }}</span></div>
        </div>
        @endif
    </div>

    <!-- Gold Items Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
        <div style="background:#1e3a5f;padding:6px 10px;">
            <p style="color:#93c5fd;font-size:10px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;">💎 Gold Items</p>
        </div>
        @foreach ($breakdown as $index => $item)
        <div class="row {{ !$loop->last ? '' : '' }}">
            <div class="lbl">Item {{ $index + 1 }}</div>
            <div class="val" style="flex-direction:column;align-items:flex-start;padding-top:7px;padding-bottom:7px;">
                <span class="text-sm font-bold text-gray-900">{{ number_format($item['grams'], 2) }}g {{ $item['karat'] }}K</span>
                <span style="font-size:11px;color:#6b7280;">@ ₱{{ number_format($item['price_per_gram'], 0) }}/g = <strong style="color:#2563eb;">₱{{ number_format($item['total'], 0) }}</strong></span>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Calculation Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
        <div style="background:#1e3a5f;padding:6px 10px;">
            <p style="color:#93c5fd;font-size:10px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;">📊 Calculation</p>
        </div>
        @if($todayGoldPrice)
        <div class="row">
            <div class="lbl">Base Rate</div>
            <div class="val"><span class="text-sm font-bold text-indigo-600">₱{{ number_format($todayGoldPrice->daily_price, 0) }}/g</span></div>
        </div>
        @endif
        <div class="row">
            <div class="lbl">Gold Value</div>
            <div class="val"><span class="text-base font-black text-blue-600">₱{{ number_format($receipt->total_item_value, 0) }}</span></div>
        </div>
        <div class="row">
            <div class="lbl">Lukat Fee</div>
            <div class="val"><span class="text-base font-black text-amber-600">-₱{{ number_format($receipt->lukat_fee, 0) }}</span></div>
        </div>

        @if($receipt->status === 'pending' || $receipt->status === 'offered')
            @if($receipt->final_buying_price)
            <div class="row">
                <div class="lbl">Offer</div>
                <div class="val"><span class="text-base font-black text-purple-600">-₱{{ number_format($receipt->final_buying_price, 0) }}</span></div>
            </div>
            @endif
            {{-- Offer Range row --}}
            <div style="display:grid;grid-template-columns:110px 1fr;background:{{ $isNegative ? '#fef2f2' : '#f0fdf4' }};border-top:2px solid {{ $isNegative ? '#f87171' : '#4ade80' }};">
                <div class="lbl" style="background:transparent;color:{{ $isNegative ? '#dc2626' : '#16a34a' }};">Offer Range</div>
                <div class="val"><span style="font-size:18px;font-weight:900;color:{{ $isNegative ? '#dc2626' : '#16a34a' }};">₱{{ number_format($offerRange, 0) }}</span></div>
            </div>
            @if($receipt->final_buying_price)
            {{-- Profit at current offer --}}
            <div style="display:grid;grid-template-columns:110px 1fr;background:{{ $hasNegativeProfit ? '#fef2f2' : '#f9fafb' }};">
                <div class="lbl" style="background:transparent;color:{{ $hasNegativeProfit ? '#dc2626' : '#6b7280' }};">Profit</div>
                <div class="val"><span style="font-size:16px;font-weight:900;color:{{ $hasNegativeProfit ? '#dc2626' : '#16a34a' }};">₱{{ number_format($profitAtOffer, 0) }}</span></div>
            </div>
            @endif
            @if($isNegative || $hasNegativeProfit)
            <div style="background:#fee2e2;padding:6px 10px;border-top:1px solid #fca5a5;">
                <p style="font-size:11px;font-weight:700;color:#dc2626;">⚠️ {{ $isNegative ? 'Lukat exceeds gold value — negative offer range' : 'Current offer results in a loss of ₱'.number_format(abs($profitAtOffer), 0) }}</p>
            </div>
            @endif

        @elseif($receipt->status === 'completed')
            <div style="display:grid;grid-template-columns:110px 1fr;background:#f0fdf4;border-top:2px solid #4ade80;">
                <div class="lbl" style="background:transparent;color:#16a34a;">Purchase</div>
                <div class="val"><span class="text-base font-black text-green-700">-₱{{ number_format($receipt->final_buying_price, 0) }}</span></div>
            </div>
            <div style="display:grid;grid-template-columns:110px 1fr;background:#111827;">
                <div class="lbl" style="background:transparent;color:#d1d5db;">Your Profit</div>
                <div class="val"><span style="font-size:18px;font-weight:900;color:white;">₱{{ number_format(max(0, $receipt->total_item_value - $receipt->lukat_fee - $receipt->final_buying_price), 0) }}</span></div>
            </div>
        @endif
    </div>

    <!-- Note -->
    @if($receipt->note)
    <div class="bg-yellow-50 rounded-xl border border-yellow-200 px-3 py-2">
        <p style="font-size:10px;color:#b45309;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">📝 Note</p>
        <p class="text-gray-800 leading-snug whitespace-pre-wrap" style="font-size:12px;">{{ $receipt->note }}</p>
    </div>
    @endif

    <!-- Footer -->
    <p class="text-center text-gray-400 pb-1" style="font-size:10px;">
        Generated {{ now()->format('M d, Y g:i A') }}
    </p>

    <!-- Actions -->
    <div class="no-print grid grid-cols-2 gap-2">
        <button onclick="window.print()" class="flex items-center justify-center gap-1 bg-indigo-600 text-white font-bold py-2.5 rounded-xl text-xs shadow">🖨️ Print</button>
        <button onclick="copyLink()" id="copyBtn" class="flex items-center justify-center gap-1 bg-gray-700 text-white font-bold py-2.5 rounded-xl text-xs shadow">🔗 Copy Link</button>
    </div>

</div>

<script>
    function copyLink() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            const btn = document.getElementById('copyBtn');
            btn.innerHTML = '✅ Copied!';
            setTimeout(() => btn.innerHTML = '🔗 Copy Link', 2000);
        });
    }
</script>
</body>
</html>

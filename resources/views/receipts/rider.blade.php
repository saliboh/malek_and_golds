<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>🛵 Rider - {{ $receipt->receipt_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 13px; }
        .row { display: grid; grid-template-columns: 100px 1fr; border-bottom: 1px solid #f3f4f6; }
        .row:last-child { border-bottom: none; }
        .label { background: #f9fafb; padding: 7px 10px; font-size: 11px; font-weight: 600; color: #9ca3af; display: flex; align-items: flex-start; padding-top: 9px; }
        .value { padding: 7px 10px; display: flex; align-items: center; }
    </style>
</head>
<body class="bg-gray-100">

    <div class="max-w-sm mx-auto px-3 pt-3 pb-4 space-y-2">

        <!-- Header -->
        <div class="bg-orange-500 rounded-xl px-3 py-2.5 flex items-center justify-between">
            <div>
                <p class="text-white font-black text-sm leading-tight">🛵 Rider Dispatch</p>
                <p class="text-orange-100 text-xs">#{{ $receipt->receipt_number }} · {{ $receipt->created_at->format('M d, Y') }}</p>
            </div>
            <span class="text-xs bg-white/20 text-white font-semibold px-2 py-0.5 rounded-lg">{{ $receipt->source ?? 'Malek & Golds' }}</span>
        </div>

        <!-- Info Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
            <div class="row">
                <div class="label">FB Page</div>
                <div class="value"><span class="text-sm font-bold text-gray-900">{{ $receipt->source ?? 'Malek & Golds' }}</span></div>
            </div>
            <div class="row">
                <div class="label">FB Name</div>
                <div class="value"><span class="text-sm font-black text-gray-900">{{ $receipt->owner_name ?? '—' }}</span></div>
            </div>
            <div class="row">
                <div class="label">Contact</div>
                <div class="value">
                    @if($receipt->owner_contact)
                        <a href="tel:{{ $receipt->owner_contact }}" class="text-sm font-bold text-blue-600 underline underline-offset-2">{{ $receipt->owner_contact }}</a>
                    @else
                        <span class="text-sm text-gray-400 italic">—</span>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="label">Pawnshop</div>
                <div class="value"><span class="text-sm font-semibold text-gray-900">{{ $receipt->pawn_shop_name ?? '—' }}</span></div>
            </div>
            <div class="row" style="border-bottom:none">
                <div class="label">Address</div>
                <div class="value" style="align-items:flex-start; padding-top:9px;">
                    @if($receipt->address)
                        <span class="text-sm font-semibold text-gray-900 leading-snug">{{ $receipt->address }}</span>
                    @else
                        <span class="text-sm text-gray-400 italic">—</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Payment Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
            @php $offer = $receipt->final_buying_price; @endphp
            <div class="row">
                <div class="label">Total Lukat</div>
                <div class="value"><span class="text-base font-black text-amber-600">₱{{ number_format($receipt->lukat_fee, 0) }}</span></div>
            </div>
            <div class="row" style="border-bottom:none">
                <div class="label">Palit Resibo</div>
                <div class="value">
                    @if($offer)
                        <span class="text-base font-black text-green-700">₱{{ number_format($offer, 0) }}</span>
                    @else
                        <span class="text-sm text-red-400 font-semibold italic">Not set</span>
                    @endif
                </div>
            </div>
            <div style="display:grid; grid-template-columns: 100px 1fr; background:#111827;">
                <div style="padding:7px 10px; font-size:11px; font-weight:700; color:#d1d5db; display:flex; align-items:center;">Total to Bring</div>
                <div style="padding:7px 10px; display:flex; align-items:center;">
                    @if($offer)
                        <span class="text-lg font-black text-white">₱{{ number_format($receipt->lukat_fee + $offer, 0) }}</span>
                    @else
                        <span style="font-size:13px; color:#6b7280; font-style:italic;">—</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($receipt->note)
        <div class="bg-yellow-50 rounded-xl border border-yellow-200 px-3 py-2">
            <p class="text-yellow-600 font-bold uppercase tracking-widest mb-0.5" style="font-size:10px;">📝 Notes</p>
            <p class="text-gray-800 leading-snug whitespace-pre-wrap" style="font-size:12px;">{{ $receipt->note }}</p>
        </div>
        @endif

        <!-- Actions -->
        <button onclick="copyLink()" id="copyBtn" class="w-full flex items-center justify-center gap-1 bg-gray-700 text-white font-bold py-2.5 rounded-xl text-xs shadow">🔗 Copy Link</button>

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


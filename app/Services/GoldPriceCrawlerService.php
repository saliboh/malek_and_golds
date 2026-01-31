<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class GoldPriceCrawlerService
{
    private const GOLD_PRICE_URL = 'https://goldpricez.com/ph/gram';
    private const GOLD_PRICE_SELECTOR = '#gold_price';

    /**
     * Fetch the latest gold price from goldpricez.com (Market Price)
     * Does NOT save to database - only displays for reference
     *
     * @return array|null Array with 'price' and 'timestamp' or null if failed
     */
    public function fetchMarketGoldPrice(): ?array
    {
        try {
            $response = Http::timeout(10)->get(self::GOLD_PRICE_URL);

            if (!$response->successful()) {
                return null;
            }

            $html = $response->body();
            $crawler = new Crawler($html);

            // Extract the gold price from the span with id="gold_price"
            $priceText = $crawler->filter(self::GOLD_PRICE_SELECTOR)->text();

            // Remove non-numeric characters except decimal point and extract the number
            $price = floatval(preg_replace('/[^0-9.]/', '', $priceText));

            if ($price <= 0) {
                return null;
            }

            return [
                'price' => $price,
                'timestamp' => now(),
                'source' => 'goldpricez.com',
            ];
        } catch (\Exception $e) {
            Log::error('Gold Price Crawler Error: ' . $e->getMessage());
            return null;
        }
    }
}


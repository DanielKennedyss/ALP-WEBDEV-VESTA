<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    /**
     * Get provinces list from RajaOngkir via Komerce Proxy
     */
    public function get_provinces()
    {
        $apiKey = env('RAJAONGKIR_API_KEY');
        
        try {
            $response = Http::withHeaders([
                'key' => $apiKey
            ])->get('https://rajaongkir.komerce.id/api/v1/destination/province');

            if ($response->successful()) {
                $results = $response->json()['data'] ?? [];
                
                // Map Komerce keys (id, name) to standard RajaOngkir keys (province_id, province)
                $provinces = collect($results)->map(function ($p) {
                    return [
                        'province_id' => $p['id'],
                        'province' => $p['name']
                    ];
                })->toArray();

                return response()->json([
                    'success' => true,
                    'data' => $provinces
                ]);
            }

            Log::error('Komerce Provinces API failed: ' . $response->body());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch provinces from Komerce RajaOngkir.'
            ], 400);

        } catch (\Exception $e) {
            Log::error('RajaOngkir Provinces Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching provinces.'
            ], 500);
        }
    }

    /**
     * Get cities list by province ID from RajaOngkir via Komerce Proxy
     */
    public function get_cities($province_id)
    {
        $apiKey = env('RAJAONGKIR_API_KEY');
        
        try {
            $response = Http::withHeaders([
                'key' => $apiKey
            ])->get("https://rajaongkir.komerce.id/api/v1/destination/city/{$province_id}");

            if ($response->successful()) {
                $results = $response->json()['data'] ?? [];
                
                // Map Komerce keys (id, name) to standard RajaOngkir keys (city_id, city_name)
                $cities = collect($results)->map(function ($c) {
                    return [
                        'city_id' => $c['id'],
                        'city_name' => $c['name'],
                        'type' => ''
                    ];
                })->toArray();

                return response()->json([
                    'success' => true,
                    'data' => $cities
                ]);
            }

            Log::error('Komerce Cities API failed: ' . $response->body());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cities from Komerce RajaOngkir.'
            ], 400);

        } catch (\Exception $e) {
            Log::error('RajaOngkir Cities Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching cities.'
            ], 500);
        }
    }

    /**
     * Calculate shipping cost from Surabaya (origin: 444) to destination city via Komerce Proxy
     */
    public function get_shipping_cost(Request $request)
    {
        $request->validate([
            'destination_city_id' => 'required|integer',
            'courier' => 'required|string|in:jne,pos,tiki',
        ]);

        $apiKey = env('RAJAONGKIR_API_KEY');
        $originCityId = 444; // Surabaya City ID in RajaOngkir / Komerce
        
        // Calculate total weight of the cart items
        $isBuyNow = session()->has('buy_now');
        $cartItems = $isBuyNow ? [session('buy_now')] : session('cart', []);

        if (empty($cartItems)) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty.'
            ], 400);
        }

        $totalWeight = 0;
        foreach ($cartItems as $item) {
            $product = Product::find($item['product_id']);
            $weight = $product ? ($product->weight ?? 500) : 500; // default to 500g if weight is null
            $totalWeight += ($weight * $item['quantity']);
        }

        // If totalWeight is 0 or very light, default to minimum 100g to keep RajaOngkir happy
        if ($totalWeight <= 0) {
            $totalWeight = 100;
        }

        try {
            $response = Http::withHeaders([
                'key' => $apiKey
            ])->asForm()->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
                'origin' => $originCityId,
                'destination' => $request->destination_city_id,
                'weight' => $totalWeight,
                'courier' => strtolower($request->courier),
            ]);

            if ($response->successful()) {
                $results = $response->json()['data'] ?? [];
                
                // Map the results to a simplified format for AJAX
                $services = collect($results)->map(function ($cost) {
                    return [
                        'service' => $cost['service'],
                        'description' => $cost['description'] ?? '',
                        'cost' => $cost['cost'] ?? 0,
                        'etd' => $cost['etd'] ?? ''
                    ];
                });

                return response()->json([
                    'success' => true,
                    'total_weight_grams' => $totalWeight,
                    'services' => $services
                ]);
            }

            $errorMessage = $response->json()['meta']['message'] ?? 'Failed to calculate shipping cost from Komerce.';
            Log::error('Komerce Calculate API failed: ' . $response->body());
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 400);

        } catch (\Exception $e) {
            Log::error('RajaOngkir Cost Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while calculating shipping cost.'
            ], 500);
        }
    }
}


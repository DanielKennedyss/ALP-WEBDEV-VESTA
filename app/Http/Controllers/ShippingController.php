<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    /**
     * Get static provinces list to match Biteship workflow
     */
    public function get_provinces()
    {
        $provinces = [
            ['province_id' => 'bali', 'province' => 'BALI'],
            ['province_id' => 'bangka_belitung', 'province' => 'BANGKA BELITUNG'],
            ['province_id' => 'banten', 'province' => 'BANTEN'],
            ['province_id' => 'bengkulu', 'province' => 'BENGKULU'],
            ['province_id' => 'di_yogyakarta', 'province' => 'DI YOGYAKARTA'],
            ['province_id' => 'dki_jakarta', 'province' => 'DKI JAKARTA'],
            ['province_id' => 'gorontalo', 'province' => 'GORONTALO'],
            ['province_id' => 'jambi', 'province' => 'JAMBI'],
            ['province_id' => 'jawa_barat', 'province' => 'JAWA BARAT'],
            ['province_id' => 'jawa_tengah', 'province' => 'JAWA TENGAH'],
            ['province_id' => 'jawa_timur', 'province' => 'JAWA TIMUR'],
            ['province_id' => 'kalimantan_barat', 'province' => 'KALIMANTAN BARAT'],
            ['province_id' => 'kalimantan_selatan', 'province' => 'KALIMANTAN SELATAN'],
            ['province_id' => 'kalimantan_tengah', 'province' => 'KALIMANTAN TENGAH'],
            ['province_id' => 'kalimantan_timur', 'province' => 'KALIMANTAN TIMUR'],
            ['province_id' => 'kalimantan_utara', 'province' => 'KALIMANTAN UTARA'],
            ['province_id' => 'kepulauan_riau', 'province' => 'KEPULAUAN RIAU'],
            ['province_id' => 'lampung', 'province' => 'LAMPUNG'],
            ['province_id' => 'maluku', 'province' => 'MALUKU'],
            ['province_id' => 'maluku_utara', 'province' => 'MALUKU UTARA'],
            ['province_id' => 'nanggroe_aceh_darussalam', 'province' => 'NANGGROE ACEH DARUSSALAM (NAD)'],
            ['province_id' => 'nusa_tenggara_barat', 'province' => 'NUSA TENGGARA BARAT (NTB)'],
            ['province_id' => 'nusa_tenggara_timur', 'province' => 'NUSA TENGGARA TIMUR (NTT)'],
            ['province_id' => 'papua', 'province' => 'PAPUA'],
            ['province_id' => 'papua_barat', 'province' => 'PAPUA BARAT'],
            ['province_id' => 'riau', 'province' => 'RIAU'],
            ['province_id' => 'sulawesi_barat', 'province' => 'SULAWESI BARAT'],
            ['province_id' => 'sulawesi_selatan', 'province' => 'SULAWESI SELATAN'],
            ['province_id' => 'sulawesi_tengah', 'province' => 'SULAWESI TENGAH'],
            ['province_id' => 'sulawesi_tenggara', 'province' => 'SULAWESI TENGGARA'],
            ['province_id' => 'sulawesi_utara', 'province' => 'SULAWESI UTARA'],
            ['province_id' => 'sumatera_barat', 'province' => 'SUMATERA BARAT'],
            ['province_id' => 'sumatera_selatan', 'province' => 'SUMATERA SELATAN'],
            ['province_id' => 'sumatera_utara', 'province' => 'SUMATERA UTARA']
        ];

        return response()->json([
            'success' => true,
            'data' => $provinces
        ]);
    }

    /**
     * Get subdistricts/cities list from Biteship by matching the selected province
     */
    public function get_cities($province_id)
    {
        $apiKey = env('BITESHIP_API_KEY');
        
        $provinceNames = [
            'bali' => 'Bali',
            'bangka_belitung' => 'Bangka Belitung',
            'banten' => 'Banten',
            'bengkulu' => 'Bengkulu',
            'di_yogyakarta' => 'DI Yogyakarta',
            'dki_jakarta' => 'DKI Jakarta',
            'gorontalo' => 'Gorontalo',
            'jambi' => 'Jambi',
            'jawa_barat' => 'Jawa Barat',
            'jawa_tengah' => 'Jawa Tengah',
            'jawa_timur' => 'Jawa Timur',
            'kalimantan_barat' => 'Kalimantan Barat',
            'kalimantan_selatan' => 'Kalimantan Selatan',
            'kalimantan_tengah' => 'Kalimantan Tengah',
            'kalimantan_timur' => 'Kalimantan Timur',
            'kalimantan_utara' => 'Kalimantan Utara',
            'kepulauan_riau' => 'Kepulauan Riau',
            'lampung' => 'Lampung',
            'maluku' => 'Maluku',
            'maluku_utara' => 'Maluku Utara',
            'nanggroe_aceh_darussalam' => 'Aceh',
            'nusa_tenggara_barat' => 'Nusa Tenggara Barat',
            'nusa_tenggara_timur' => 'Nusa Tenggara Timur',
            'papua' => 'Papua',
            'papua_barat' => 'Papua Barat',
            'riau' => 'Riau',
            'sulawesi_barat' => 'Sulawesi Barat',
            'sulawesi_selatan' => 'Sulawesi Selatan',
            'sulawesi_tengah' => 'Sulawesi Tengah',
            'sulawesi_tenggara' => 'Sulawesi Tenggara',
            'sulawesi_utara' => 'Sulawesi Utara',
            'sumatera_barat' => 'Sumatera Barat',
            'sumatera_selatan' => 'Sumatera Selatan',
            'sumatera_utara' => 'Sumatera Utara'
        ];

        $provinceName = $provinceNames[strtolower($province_id)] ?? $province_id;
        
        try {
            $response = Http::withHeaders([
                'Authorization' => $apiKey
            ])->get('https://api.biteship.com/v1/maps/areas', [
                'input' => $provinceName,
                'countries' => 'ID'
            ]);

            if ($response->successful()) {
                $results = $response->json()['areas'] ?? [];
                
                $cities = collect($results)->map(function ($area) {
                    $name = $area['name'] ?? 'Unknown Area';
                    preg_match('/(\d{5})$/', $name, $matches);
                    $postalCode = $matches[1] ?? '';
                    
                    return [
                        'city_id' => $area['id'],
                        'city_name' => $name,
                        'postal_code' => $postalCode,
                        'type' => ''
                    ];
                })->sortBy('city_name', SORT_NATURAL | SORT_FLAG_CASE)->values()->toArray();


                return response()->json([
                    'success' => true,
                    'data' => $cities
                ]);
            }

            Log::error('Biteship Cities API failed: ' . $response->body());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cities from Biteship.'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Biteship Cities Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching cities.'
            ], 500);
        }
    }

    /**
     * Calculate shipping cost using Biteship rates API
     */
    public function get_shipping_cost(Request $request)
    {
        $request->validate([
            'destination_city_id' => 'required|string',
            'destination_postal_code' => 'nullable|string',
            'courier' => 'required|string',
        ]);

        $apiKey = env('BITESHIP_API_KEY');
        $originPostalCode = 60261; // Surabaya, Tegalsari postal code in Biteship
        
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
        $items = [];
        foreach ($cartItems as $item) {
            $product = Product::find($item['product_id']);
            $weight = $product ? ($product->weight ?? 500) : 500;
            $totalWeight += ($weight * $item['quantity']);
            
            $items[] = [
                'name' => substr($item['name'], 0, 50),
                'value' => (int) $item['price'],
                'quantity' => (int) $item['quantity'],
                'weight' => (int) $weight
            ];
        }

        if ($totalWeight <= 0) {
            $totalWeight = 100;
        }

        try {
            $ratesParams = [
                'origin_postal_code' => $originPostalCode,
                'couriers' => strtolower($request->courier),
                'items' => $items
            ];
            
            if ($request->filled('destination_postal_code')) {
                $ratesParams['destination_postal_code'] = (int) $request->destination_postal_code;
            } else {
                $ratesParams['destination_area_id'] = $request->destination_city_id;
            }

            $response = Http::withHeaders([
                'Authorization' => $apiKey,
                'Content-Type' => 'application/json'
            ])->post('https://api.biteship.com/v1/rates/couriers', $ratesParams);

            if ($response->successful()) {
                $pricing = $response->json()['pricing'] ?? [];
                
                $services = collect($pricing)->map(function ($rate) {
                    return [
                        'service' => strtoupper($rate['courier_service_code'] ?? $rate['courier_service_name']),
                        'description' => $rate['courier_service_name'] . ' (' . ($rate['description'] ?? '') . ')',
                        'cost' => $rate['price'] ?? 0,
                        'etd' => $rate['duration'] ?? ''
                    ];
                })->values()->toArray();

                return response()->json([
                    'success' => true,
                    'total_weight_grams' => $totalWeight,
                    'services' => $services
                ]);
            }

            $responseJson = $response->json();
            $errorMessage = $responseJson['message'] ?? $responseJson['error'] ?? 'Failed to calculate shipping cost from Biteship.';
            Log::error('Biteship Calculate API failed: ' . $response->body());
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 400);

        } catch (\Exception $e) {
            Log::error('Biteship Cost Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while calculating shipping cost.'
            ], 500);
        }
    }
}

<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RajaOngkirController extends Controller
{
    public function getProvinces()
    {
        try {
            $response = Http::withHeaders([
                'key' => env('RAJAONGKIR_API_KEY')
            ])->get(env('RAJAONGKIR_BASE_URL') . '/province');
            
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCities(Request $request)
    {
        try {
            $provinceId = $request->input('province_id');
            $response = Http::withHeaders([
                'key' => env('RAJAONGKIR_API_KEY')
            ])->get(env('RAJAONGKIR_BASE_URL') . '/city', [
                'province' => $provinceId
            ]);
            
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCost(Request $request)
    {
        try {
            $origin = $request->input('origin');
            $destination = $request->input('destination');
            $weight = $request->input('weight');
            $courier = $request->input('courier');
            
            $response = Http::withHeaders([
                'key' => env('RAJAONGKIR_API_KEY')
            ])->post(env('RAJAONGKIR_BASE_URL') . '/cost', [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier
            ]);
            
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
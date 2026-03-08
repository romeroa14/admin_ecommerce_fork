<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ExchangeRateService
{
    /**
     * Obtiene la tasa oficial del BCV
     */
    public static function getOfficialRate(): ?float
    {
        // Cache por 3540 segundos (59 minutos) para garantizar refresco de API constante pero seguro
        return Cache::remember('bcv_official_rate', 3540, function () {
            try {
                $response = Http::timeout(5)->get('https://ve.dolarapi.com/v1/dolares/oficial');
                if ($response->successful() && isset($response->json()['promedio'])) {
                    return (float) $response->json()['promedio'];
                }
            } catch (\Exception $e) {
                Log::error('Error fetching official exchange rate: ' . $e->getMessage());
            }
            return null;
        });
    }

    /**
     * Obtiene la tasa del Dólar Paralelo
     */
    public static function getParallelRate(): ?float
    {
        return Cache::remember('bcv_parallel_rate', 1800, function () {
            try {
                $response = Http::timeout(5)->get('https://ve.dolarapi.com/v1/dolares/paralelo');
                if ($response->successful() && isset($response->json()['promedio'])) {
                    return (float) $response->json()['promedio'];
                }
            } catch (\Exception $e) {
                Log::error('Error fetching parallel exchange rate: ' . $e->getMessage());
            }
            return null;
        });
    }
}

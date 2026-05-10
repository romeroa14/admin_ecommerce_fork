<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class WebhookController extends Controller
{
    public function syncProducts(Request $request): \Illuminate\Http\JsonResponse
    {
        $token = $request->query('token');
        $expectedToken = config('services.sync_webhook_token');

        if (!$expectedToken || $token !== $expectedToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Artisan::call('products:sync-sheet');
        $output = Artisan::output();

        return response()->json([
            'status' => 'ok',
            'output' => $output,
        ]);
    }
}

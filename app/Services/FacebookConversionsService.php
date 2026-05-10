<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookConversionsService
{
    private string $pixelId;
    private string $token;
    private string $apiVersion;
    private ?string $testEventCode;

    public function __construct()
    {
        $this->pixelId = config('services.facebook.pixel_id');
        $this->token = config('services.facebook.capi_token');
        $this->apiVersion = config('services.facebook.api_version', 'v22.0');
        $this->testEventCode = config('services.facebook.test_event_code');
    }

    /**
     * Send a server-side event to Facebook Conversions API.
     */
    public function sendEvent(
        string $eventName,
        float $eventTime,
        array $userData = [],
        array $customData = [],
        ?string $eventId = null,
        string $actionSource = 'website',
        ?string $eventSourceUrl = null,
    ): bool {
        if (empty($this->pixelId) || empty($this->token)) {
            Log::warning('Facebook CAPI: pixel_id or token not configured, skipping event.');
            return false;
        }

        $payload = [
            'event_name' => $eventName,
            'event_time' => (int) $eventTime,
            'action_source' => $actionSource,
            'user_data' => $this->hashUserData($userData),
        ];

        if ($eventSourceUrl) {
            $payload['event_source_url'] = $eventSourceUrl;
        }

        if ($eventId) {
            $payload['event_id'] = $eventId;
        }

        if (!empty($customData)) {
            $payload['custom_data'] = $customData;
        }

        $body = ['data' => [$payload]];

        if ($this->testEventCode) {
            $body['test_event_code'] = $this->testEventCode;
        }

        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->pixelId}/events";

        try {
            $response = Http::timeout(10)
                ->withToken($this->token)
                ->post($url, $body);

            if ($response->successful()) {
                Log::debug("Facebook CAPI: {$eventName} event sent successfully.");
                return true;
            }

            Log::warning("Facebook CAPI: {$eventName} failed.", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error("Facebook CAPI: {$eventName} error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Hash user data fields as required by Facebook (SHA256).
     */
    private function hashUserData(array $userData): array
    {
        $hashed = [];

        $hashableFields = ['em', 'ph', 'fn', 'ln', 'ct', 'st', 'zp', 'country', 'external_id'];

        foreach ($userData as $key => $value) {
            if (in_array($key, $hashableFields) && !empty($value)) {
                $hashed[$key] = [hash('sha256', strtolower(trim($value)))];
            } elseif ($key === 'client_ip_address' || $key === 'client_user_agent' || $key === 'fbc' || $key === 'fbp') {
                // These fields must NOT be hashed
                $hashed[$key] = $value;
            }
        }

        return $hashed;
    }

    /**
     * Generate a unique event ID for deduplication between browser pixel and server.
     */
    public static function generateEventId(): string
    {
        return bin2hex(random_bytes(16));
    }
}

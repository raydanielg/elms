<?php

namespace App\Services;

use App\Models\WebhookEndpoint;
use App\Models\WebhookDispatch;
use Illuminate\Support\Facades\Http;

class WebhookDispatcher
{
    public static function dispatch(string $event, array $payload, ?int $tenantId = null): void
    {
        $endpoints = WebhookEndpoint::where('is_active', true)
            ->where(function ($q) use ($tenantId) {
                $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
            })
            ->get();

        foreach ($endpoints as $endpoint) {
            if (!$endpoint->listensTo($event)) continue;

            $signature = null;
            if ($endpoint->secret) {
                $signature = hash_hmac('sha256', json_encode($payload), $endpoint->secret);
            }

            $dispatch = WebhookDispatch::create([
                'webhook_endpoint_id' => $endpoint->id,
                'event' => $event,
                'payload' => $payload,
                'status' => 'pending',
            ]);

            try {
                $response = Http::withHeaders(array_filter([
                    'X-ELMS-Event' => $event,
                    'X-ELMS-Signature' => $signature,
                ]))->timeout(10)->post($endpoint->url, $payload);

                $dispatch->update([
                    'response_code' => $response->status(),
                    'status' => $response->successful() ? 'delivered' : 'failed',
                ]);
            } catch (\Exception $e) {
                $dispatch->update(['status' => 'failed']);
            }
        }
    }
}

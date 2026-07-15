<?php

namespace App\Services\Payment\Drivers;

use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;

class StripeDriver implements PaymentGatewayInterface
{
    protected array $credentials;

    public function __construct(array $credentials = [])
    {
        $this->credentials = $credentials;
    }

    public function initiate(array $data): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . ($this->credentials['secret_key'] ?? ''),
        ])->post('https://api.stripe.com/v1/checkout/sessions', [
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => $data['currency'] ?? 'usd',
                    'product_data' => ['name' => $data['description'] ?? 'ELMS Purchase'],
                    'unit_amount' => (int)($data['amount'] * 100),
                ],
                'quantity' => 1,
            ]],
            'success_url' => $data['success_url'] ?? route('home'),
            'cancel_url' => $data['cancel_url'] ?? route('home'),
            'metadata' => $data['metadata'] ?? [],
        ]);

        return [
            'success' => $response->successful(),
            'reference' => $response->json('id'),
            'redirect_url' => $response->json('url'),
            'raw' => $response->json(),
        ];
    }

    public function verify(string $reference): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . ($this->credentials['secret_key'] ?? ''),
        ])->get("https://api.stripe.com/v1/checkout/sessions/{$reference}");

        return [
            'success' => $response->successful(),
            'status' => $response->json('payment_status'),
            'amount' => $response->json('amount_total') ? $response->json('amount_total') / 100 : 0,
            'raw' => $response->json(),
        ];
    }

    public function handleWebhook(array $payload): array
    {
        $event = $payload['type'] ?? 'unknown';
        $object = $payload['data']['object'] ?? [];

        return [
            'event' => $event,
            'status' => $object['payment_status'] ?? 'unknown',
            'reference' => $object['id'] ?? null,
            'raw' => $payload,
        ];
    }

    public function refund(string $reference, float $amount): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . ($this->credentials['secret_key'] ?? ''),
        ])->asForm()->post('https://api.stripe.com/v1/refunds', [
            'payment_intent' => $reference,
            'amount' => (int)($amount * 100),
        ]);

        return ['success' => $response->successful(), 'raw' => $response->json()];
    }

    public function payout(array $data): array
    {
        return ['success' => false, 'message' => 'Use Stripe Connect for payouts'];
    }
}

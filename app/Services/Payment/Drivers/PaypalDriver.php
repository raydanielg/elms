<?php

namespace App\Services\Payment\Drivers;

use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;

class PaypalDriver implements PaymentGatewayInterface
{
    protected array $credentials;

    public function __construct(array $credentials = [])
    {
        $this->credentials = $credentials;
    }

    protected function getAccessToken(): string
    {
        $response = Http::withBasicAuth(
            $this->credentials['client_id'] ?? '',
            $this->credentials['client_secret'] ?? ''
        )->asForm()->post('https://api-m.paypal.com/v1/oauth2/token', [
            'grant_type' => 'client_credentials',
        ]);
        return $response->json('access_token');
    }

    public function initiate(array $data): array
    {
        $token = $this->getAccessToken();
        $response = Http::withToken($token)->post('https://api-m.paypal.com/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => $data['currency'] ?? 'USD',
                    'value' => number_format($data['amount'], 2, '.', ''),
                ],
                'description' => $data['description'] ?? 'ELMS Purchase',
            ]],
            'application_context' => [
                'return_url' => $data['success_url'] ?? route('home'),
                'cancel_url' => $data['cancel_url'] ?? route('home'),
            ],
        ]);

        $approveUrl = collect($response->json('links') ?? [])->firstWhere('rel', 'approve')['href'] ?? null;

        return [
            'success' => $response->successful(),
            'reference' => $response->json('id'),
            'redirect_url' => $approveUrl,
            'raw' => $response->json(),
        ];
    }

    public function verify(string $reference): array
    {
        $token = $this->getAccessToken();
        $response = Http::withToken($token)->get("https://api-m.paypal.com/v2/checkout/orders/{$reference}");

        return [
            'success' => $response->successful(),
            'status' => $response->json('status'),
            'amount' => (float)($response->json('purchase_units.0.amount.value') ?? 0),
            'raw' => $response->json(),
        ];
    }

    public function handleWebhook(array $payload): array
    {
        return [
            'event' => $payload['event_type'] ?? 'unknown',
            'status' => $payload['resource']['status'] ?? 'unknown',
            'reference' => $payload['resource']['id'] ?? null,
            'raw' => $payload,
        ];
    }

    public function refund(string $reference, float $amount): array
    {
        $token = $this->getAccessToken();
        $response = Http::withToken($token)->post("https://api-m.paypal.com/v2/payments/captures/{$reference}/refund", [
            'amount' => ['value' => number_format($amount, 2, '.', ''), 'currency_code' => 'USD'],
        ]);
        return ['success' => $response->successful(), 'raw' => $response->json()];
    }

    public function payout(array $data): array
    {
        return ['success' => false, 'message' => 'PayPal payouts require Payouts API setup'];
    }
}

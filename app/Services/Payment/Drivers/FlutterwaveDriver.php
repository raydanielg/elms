<?php

namespace App\Services\Payment\Drivers;

use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;

class FlutterwaveDriver implements PaymentGatewayInterface
{
    protected array $credentials;

    public function __construct(array $credentials = [])
    {
        $this->credentials = $credentials;
    }

    public function initiate(array $data): array
    {
        $response = Http::withToken($this->credentials['secret_key'] ?? '')
            ->post('https://api.flutterwave.com/v3/payments', [
                'tx_ref' => $data['reference'] ?? uniqid('elms_'),
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'USD',
                'redirect_url' => $data['success_url'] ?? route('home'),
                'customer' => [
                    'email' => $data['email'] ?? '',
                    'name' => $data['name'] ?? '',
                    'phonenumber' => $data['phone'] ?? '',
                ],
                'customizations' => [
                    'title' => 'ELMS Payment',
                    'description' => $data['description'] ?? 'ELMS Purchase',
                ],
                'payment_options' => $data['payment_options'] ?? 'card,mobilemoney,ussd',
            ]);

        return [
            'success' => $response->successful(),
            'reference' => $data['reference'] ?? uniqid('elms_'),
            'redirect_url' => $response->json('data.link'),
            'raw' => $response->json(),
        ];
    }

    public function verify(string $reference): array
    {
        $response = Http::withToken($this->credentials['secret_key'] ?? '')
            ->get("https://api.flutterwave.com/v3/transactions/{$reference}/verify");

        return [
            'success' => $response->successful(),
            'status' => $response->json('data.status'),
            'amount' => (float)($response->json('data.amount') ?? 0),
            'raw' => $response->json(),
        ];
    }

    public function handleWebhook(array $payload): array
    {
        $event = $payload['event'] ?? 'unknown';
        $data = $payload['data'] ?? [];

        return [
            'event' => $event,
            'status' => $data['status'] ?? 'unknown',
            'reference' => $data['tx_ref'] ?? $data['id'] ?? null,
            'amount' => $data['amount'] ?? null,
            'raw' => $payload,
        ];
    }

    public function refund(string $reference, float $amount): array
    {
        $response = Http::withToken($this->credentials['secret_key'] ?? '')
            ->post("https://api.flutterwave.com/v3/transactions/{$reference}/refund", [
                'amount' => $amount,
            ]);

        return ['success' => $response->successful(), 'raw' => $response->json()];
    }

    public function payout(array $data): array
    {
        $response = Http::withToken($this->credentials['secret_key'] ?? '')
            ->post('https://api.flutterwave.com/v3/transfers', [
                'account_bank' => $data['bank_code'] ?? 'MPS',
                'account_number' => $data['account_number'] ?? '',
                'amount' => $data['amount'],
                'narration' => $data['narration'] ?? 'ELMS Payout',
                'currency' => $data['currency'] ?? 'USD',
                'beneficiary_name' => $data['name'] ?? '',
            ]);

        return ['success' => $response->successful(), 'raw' => $response->json()];
    }
}

<?php

namespace App\Services\Payment\Drivers;

use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;

class MpesaDriver implements PaymentGatewayInterface
{
    protected array $credentials;

    public function __construct(array $credentials = [])
    {
        $this->credentials = $credentials;
    }

    protected function getAccessToken(): string
    {
        $url = ($this->credentials['env'] ?? 'sandbox') === 'production'
            ? 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
            : 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $response = Http::withBasicAuth(
            $this->credentials['consumer_key'] ?? '',
            $this->credentials['consumer_secret'] ?? ''
        )->get($url);

        return $response->json('access_token');
    }

    public function initiate(array $data): array
    {
        $token = $this->getAccessToken();
        $url = ($this->credentials['env'] ?? 'sandbox') === 'production'
            ? 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest'
            : 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        $timestamp = now()->format('YmdHis');
        $password = base64_encode(
            ($this->credentials['short_code'] ?? '') .
            ($this->credentials['passkey'] ?? '') .
            $timestamp
        );

        $response = Http::withToken($token)->post($url, [
            'BusinessShortCode' => $this->credentials['short_code'] ?? '',
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => (int)$data['amount'],
            'PartyA' => $data['phone'] ?? '',
            'PartyB' => $this->credentials['short_code'] ?? '',
            'PhoneNumber' => $data['phone'] ?? '',
            'CallBackURL' => $data['callback_url'] ?? route('api.payments.webhook', 'mpesa'),
            'AccountReference' => $data['account_reference'] ?? 'ELMS',
            'TransactionDesc' => $data['description'] ?? 'ELMS Payment',
        ]);

        return [
            'success' => $response->successful(),
            'reference' => $response->json('CheckoutRequestID'),
            'redirect_url' => null,
            'raw' => $response->json(),
        ];
    }

    public function verify(string $reference): array
    {
        return ['success' => false, 'message' => 'M-Pesa verification is done via webhook callback'];
    }

    public function handleWebhook(array $payload): array
    {
        $callback = $payload['Body']['stkCallback'] ?? [];
        $resultCode = $callback['ResultCode'] ?? 1;
        $items = $callback['CallbackMetadata']['Item'] ?? [];

        $amount = null;
        $mpesaRef = null;
        foreach ($items as $item) {
            if ($item['Name'] === 'Amount') $amount = $item['Value'];
            if ($item['Name'] === 'MpesaReceiptNumber') $mpesaRef = $item['Value'];
        }

        return [
            'event' => 'stk_callback',
            'status' => $resultCode === 0 ? 'paid' : 'failed',
            'reference' => $callback['CheckoutRequestID'] ?? null,
            'mpesa_reference' => $mpesaRef,
            'amount' => $amount,
            'raw' => $payload,
        ];
    }

    public function refund(string $reference, float $amount): array
    {
        return ['success' => false, 'message' => 'M-Pesa refunds require B2C API'];
    }

    public function payout(array $data): array
    {
        $token = $this->getAccessToken();
        $url = ($this->credentials['env'] ?? 'sandbox') === 'production'
            ? 'https://api.safaricom.co.ke/mpesa/b2c/v1/paymentrequest'
            : 'https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest';

        $response = Http::withToken($token)->post($url, [
            'InitiatorName' => $this->credentials['initiator_name'] ?? '',
            'SecurityCredential' => $this->credentials['security_credential'] ?? '',
            'CommandID' => 'BusinessPayment',
            'Amount' => (int)$data['amount'],
            'PartyA' => $this->credentials['short_code'] ?? '',
            'PartyB' => $data['phone'] ?? '',
            'Remarks' => $data['remarks'] ?? 'ELMS Payout',
            'QueueTimeOutURL' => $data['timeout_url'] ?? route('home'),
            'ResultURL' => $data['result_url'] ?? route('home'),
            'Occasion' => 'ELMS Withdrawal',
        ]);

        return ['success' => $response->successful(), 'raw' => $response->json()];
    }
}

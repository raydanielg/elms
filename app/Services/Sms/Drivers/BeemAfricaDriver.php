<?php

namespace App\Services\Sms\Drivers;

use App\Services\Sms\SmsGatewayInterface;
use Illuminate\Support\Facades\Http;

class BeemAfricaDriver implements SmsGatewayInterface
{
    protected array $credentials;
    protected ?string $senderId;

    public function __construct(array $credentials = [], ?string $senderId = null)
    {
        $this->credentials = $credentials;
        $this->senderId = $senderId;
    }

    public function send(string $to, string $message): array
    {
        $response = Http::withBasicAuth(
            $this->credentials['api_key'] ?? '',
            $this->credentials['secret_key'] ?? ''
        )->asForm()->post('https://apisms.beem.africa/v1/send', [
            'source_addr' => $this->senderId ?? 'ELMS',
            'dest_addr' => $to,
            'message' => $message,
        ]);

        return [
            'success' => $response->successful(),
            'message_id' => $response->json('data.message_id'),
            'raw' => $response->json(),
        ];
    }

    public function sendBulk(array $recipients, string $message): array
    {
        $results = [];
        foreach ($recipients as $to) {
            $results[] = $this->send($to, $message);
        }
        return ['success' => true, 'results' => $results];
    }

    public function checkDeliveryStatus(string $messageId): array
    {
        $response = Http::withBasicAuth(
            $this->credentials['api_key'] ?? '',
            $this->credentials['secret_key'] ?? ''
        )->get("https://apisms.beem.africa/v1/delivery-reports?message_id={$messageId}");

        return [
            'success' => $response->successful(),
            'status' => $response->json('data.status'),
            'raw' => $response->json(),
        ];
    }
}

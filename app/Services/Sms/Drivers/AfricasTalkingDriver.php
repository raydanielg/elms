<?php

namespace App\Services\Sms\Drivers;

use App\Services\Sms\SmsGatewayInterface;
use Illuminate\Support\Facades\Http;

class AfricasTalkingDriver implements SmsGatewayInterface
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
        $url = ($this->credentials['env'] ?? 'sandbox') === 'production'
            ? 'https://api.africastalking.com/version1/messaging'
            : 'https://api.sandbox.africastalking.com/version1/messaging';

        $response = Http::withHeaders([
            'apiKey' => $this->credentials['api_key'] ?? '',
            'Accept' => 'application/json',
        ])->asForm()->post($url, [
            'username' => $this->credentials['username'] ?? 'sandbox',
            'to' => $to,
            'message' => $message,
            'from' => $this->senderId ?? 'ELMS',
        ]);

        return [
            'success' => $response->successful(),
            'message_id' => $response->json('SMSMessageData.MessageId'),
            'cost' => $response->json('SMSMessageData.Recipients.0.cost'),
            'raw' => $response->json(),
        ];
    }

    public function sendBulk(array $recipients, string $message): array
    {
        $to = implode(',', $recipients);
        return $this->send($to, $message);
    }

    public function checkDeliveryStatus(string $messageId): array
    {
        $response = Http::withHeaders([
            'apiKey' => $this->credentials['api_key'] ?? '',
        ])->asForm()->get('https://api.africastalking.com/version1/messaging', [
            'username' => $this->credentials['username'] ?? 'sandbox',
            'lastReceivedId' => $messageId,
        ]);

        return [
            'success' => $response->successful(),
            'status' => $response->json('SMSMessageData.Recipients.0.status'),
            'raw' => $response->json(),
        ];
    }
}

<?php

namespace App\Services\Sms\Drivers;

use App\Services\Sms\SmsGatewayInterface;
use Illuminate\Support\Facades\Http;

class TwilioDriver implements SmsGatewayInterface
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
        $sid = $this->credentials['account_sid'] ?? '';
        $token = $this->credentials['auth_token'] ?? '';
        $from = $this->senderId ?? $this->credentials['from_number'] ?? '';

        $response = Http::withBasicAuth($sid, $token)
            ->asForm()->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'From' => $from,
                'To' => $to,
                'Body' => $message,
            ]);

        return [
            'success' => $response->successful(),
            'message_id' => $response->json('sid'),
            'status' => $response->json('status'),
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
        $sid = $this->credentials['account_sid'] ?? '';
        $token = $this->credentials['auth_token'] ?? '';

        $response = Http::withBasicAuth($sid, $token)
            ->get("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages/{$messageId}.json");

        return [
            'success' => $response->successful(),
            'status' => $response->json('status'),
            'raw' => $response->json(),
        ];
    }
}

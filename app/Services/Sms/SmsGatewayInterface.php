<?php

namespace App\Services\Sms;

interface SmsGatewayInterface
{
    public function send(string $to, string $message): array;
    public function sendBulk(array $recipients, string $message): array;
    public function checkDeliveryStatus(string $messageId): array;
}

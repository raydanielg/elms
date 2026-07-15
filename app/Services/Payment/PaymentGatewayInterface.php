<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    public function initiate(array $data): array;
    public function verify(string $reference): array;
    public function handleWebhook(array $payload): array;
    public function refund(string $reference, float $amount): array;
    public function payout(array $data): array;
}

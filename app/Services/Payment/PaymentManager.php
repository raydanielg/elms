<?php

namespace App\Services\Payment;

use App\Models\PaymentGateway;
use InvalidArgumentException;

class PaymentManager
{
    protected array $drivers = [];
    protected ?PaymentGatewayInterface $driver = null;

    public function registerDriver(string $key, string $class): void
    {
        $this->drivers[$key] = $class;
    }

    public function driver(?string $key = null): PaymentGatewayInterface
    {
        if ($this->driver && !$key) return $this->driver;

        $key = $key ?? $this->getDefaultDriver();
        if (!isset($this->drivers[$key])) {
            throw new InvalidArgumentException("Payment driver [{$key}] is not registered.");
        }

        $gateway = PaymentGateway::where('driver', $key)->where('is_active', true)->first();
        $credentials = $gateway?->credentials ?? [];

        $this->driver = app($this->drivers[$key], ['credentials' => $credentials]);
        return $this->driver;
    }

    public function getDefaultDriver(): string
    {
        $gateway = PaymentGateway::active()->first();
        return $gateway?->driver ?? 'stripe';
    }

    public function availableDrivers(): array
    {
        return array_keys($this->drivers);
    }

    public function availableForCurrency(string $currency): \Illuminate\Support\Collection
    {
        return PaymentGateway::forCurrency($currency);
    }
}

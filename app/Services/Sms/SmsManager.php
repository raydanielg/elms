<?php

namespace App\Services\Sms;

use App\Models\SmsGateway;
use App\Models\SmsTemplate;
use App\Models\SmsOptOut;
use App\Models\SmsCredit;
use App\Models\NotificationTrigger;
use App\Jobs\SendSmsJob;
use InvalidArgumentException;

class SmsManager
{
    protected array $drivers = [];
    protected ?SmsGatewayInterface $driver = null;

    public function registerDriver(string $key, string $class): void
    {
        $this->drivers[$key] = $class;
    }

    public function driver(?string $key = null): SmsGatewayInterface
    {
        if ($this->driver && !$key) return $this->driver;

        $key = $key ?? $this->getDefaultDriver();
        if (!isset($this->drivers[$key])) {
            throw new InvalidArgumentException("SMS driver [{$key}] is not registered.");
        }

        $gateway = SmsGateway::where('driver', $key)->where('is_active', true)->first();
        $credentials = $gateway?->credentials ?? [];
        $senderId = $gateway?->sender_id ?? null;

        $this->driver = app($this->drivers[$key], ['credentials' => $credentials, 'senderId' => $senderId]);
        return $this->driver;
    }

    public function getDefaultDriver(): string
    {
        $gateway = SmsGateway::active();
        return $gateway?->driver ?? 'africas_talking';
    }

    public function send(string $to, string $message): array
    {
        return $this->driver()->send($to, $message);
    }

    public function sendTemplated(string $to, string $templateKey, array $data = [], string $language = 'en'): ?array
    {
        $template = SmsTemplate::render($templateKey, $data, $language);
        if (!$template) return null;

        $category = SmsTemplate::where('key', $templateKey)->value('category') ?? 'critical';

        $userId = $data['user_id'] ?? null;
        if ($userId && $category !== 'critical' && SmsOptOut::hasOptedOut($userId, $category)) {
            return ['skipped' => true, 'reason' => 'opted_out'];
        }

        return $this->driver()->send($to, $template);
    }

    public function sendBulk(array $recipients, string $message): array
    {
        return $this->driver()->sendBulk($recipients, $message);
    }

    public function dispatch(string $to, string $templateKey, array $data = [], string $language = 'en'): void
    {
        SendSmsJob::dispatch($to, $templateKey, $data, $language);
    }

    public function notifyEvent(string $event, array $data, ?int $tenantId = null): void
    {
        $trigger = NotificationTrigger::forEvent($event, $tenantId);
        if (!$trigger || !$trigger->sms_enabled) return;

        $templateKey = str_replace('.', '_', $event);
        $phone = $data['phone'] ?? null;
        $userId = $data['user_id'] ?? null;

        if (!$phone) return;

        $credits = SmsCredit::forTenant($tenantId);
        if ($credits->balance < 1) return;

        $result = $this->sendTemplated($phone, $templateKey, $data, $data['language'] ?? 'en');
        if ($result && !($result['skipped'] ?? false)) {
            $credits->deduct(1);
        }
    }
}

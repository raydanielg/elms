<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationTemplate;
use App\Models\NotificationTrigger;
use App\Services\Sms\SmsManager;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function notify(int $userId, string $type, string $title, string $body): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'body' => $body,
        ]);
    }

    public function notifyEvent(string $event, array $data, ?int $tenantId = null): void
    {
        $trigger = NotificationTrigger::forEvent($event, $tenantId);
        $templateKey = str_replace('.', '_', $event);
        $userId = $data['user_id'] ?? null;

        if (!$userId) return;

        if (!$trigger || $trigger->in_app_enabled) {
            $rendered = NotificationTemplate::render($templateKey, $data, $data['language'] ?? 'en');
            $this->notify(
                $userId,
                $data['type'] ?? 'info',
                $rendered['subject'] ?? ($data['title'] ?? 'Notification'),
                $rendered['body'] ?? ($data['body'] ?? '')
            );
        }

        if ($trigger && $trigger->sms_enabled && ($data['phone'] ?? null)) {
            app(SmsManager::class)->sendTemplated($data['phone'], $templateKey, $data, $data['language'] ?? 'en');
        }

        if ($trigger && $trigger->email_enabled && ($data['email'] ?? null)) {
            $rendered = NotificationTemplate::render($templateKey, $data, $data['language'] ?? 'en');
            if ($rendered) {
                Mail::raw($rendered['body'], function ($msg) use ($data, $rendered) {
                    $msg->to($data['email'])->subject($rendered['subject'] ?? 'ELMS Notification');
                });
            }
        }

        WebhookDispatcher::dispatch($event, $data, $tenantId);
    }
}

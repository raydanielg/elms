<?php

namespace App\Actions\Automation;

use App\Models\Notification;
use App\Models\User;

class SendNotificationAction implements AutomationActionInterface
{
    public function execute($model, array $params = []): void
    {
        $userId = $params['user_id'] ?? ($model->user_id ?? null);
        if (!$userId) return;

        Notification::create([
            'user_id' => $userId,
            'type' => $params['type'] ?? 'info',
            'title' => $params['title'] ?? 'Notification',
            'body' => $params['body'] ?? '',
        ]);
    }
}

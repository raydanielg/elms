<?php

namespace App\Actions\Automation;

use App\Services\Sms\SmsManager;
use App\Services\NotificationService;

class SendReengagementAction implements AutomationActionInterface
{
    public function execute($model, array $params = []): void
    {
        $phone = $model->phone ?? null;
        $name = $model->name ?? 'Student';

        if ($phone) {
            app(SmsManager::class)->sendTemplated($phone, 'reengagement_reminder', [
                'student_name' => $name,
            ]);
        }

        app(NotificationService::class)->notify($model->id, 'info', 'We miss you!', 'You haven\'t logged in for a while. Come back and continue learning!');
    }
}

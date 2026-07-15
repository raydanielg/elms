<?php

namespace App\Actions\Automation;

use App\Models\Tenant;
use App\Services\NotificationService;

class SuspendTenantAction implements AutomationActionInterface
{
    public function execute($model, array $params = []): void
    {
        if (!($model instanceof Tenant)) return;

        $model->update(['status' => 'suspended']);

        app(NotificationService::class)->notify(
            $model->users()->where('role', 'admin')->first()?->id,
            'error',
            'Subscription Suspended',
            'Your institution has been suspended due to payment failure. Please update your payment method.'
        );
    }
}

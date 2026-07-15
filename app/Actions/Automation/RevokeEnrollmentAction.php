<?php

namespace App\Actions\Automation;

use App\Models\Enrollment;

class RevokeEnrollmentAction implements AutomationActionInterface
{
    public function execute($model, array $params = []): void
    {
        if (!($model instanceof Enrollment)) return;

        $model->update(['status' => 'revoked']);
    }
}

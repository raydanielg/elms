<?php

namespace App\Services;

use App\Models\AutomationRule;
use Illuminate\Support\Facades\Log;

class AutomationEngine
{
    public static function trigger(string $event, $model): void
    {
        $tenantId = method_exists($model, 'tenant') ? $model->tenant?->id : null;
        $rules = AutomationRule::forEvent($event, $tenantId);

        foreach ($rules as $rule) {
            if (!$rule->passesConditions($model)) continue;

            try {
                $actionClass = $rule->action_class;
                if (class_exists($actionClass)) {
                    $action = app($actionClass);
                    $action->execute($model, $rule->action_params ?? []);
                }
            } catch (\Exception $e) {
                Log::error("Automation rule failed: {$rule->name}", [
                    'event' => $event,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}

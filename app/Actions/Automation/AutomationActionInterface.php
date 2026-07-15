<?php

namespace App\Actions\Automation;

interface AutomationActionInterface
{
    public function execute($model, array $params = []): void;
}

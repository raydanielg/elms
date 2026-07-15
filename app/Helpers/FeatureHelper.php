<?php

namespace App\Helpers;

use App\Models\FeatureFlag;
use App\Models\Tenant;

class FeatureHelper
{
    public static function isEnabled(string $key, ?Tenant $tenant = null): bool
    {
        return FeatureFlag::isEnabled($key, $tenant);
    }
}

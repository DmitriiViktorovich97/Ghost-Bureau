<?php

namespace App\Services\GhostMatcherService\Rules\Interfaces;

use App\Models\Ghost;
use App\Models\Location;
use App\Services\GhostMatcherService\Rules\DTO\ConditionResult;

interface ExtraRuleInterface
{
    public function evaluate(Ghost $ghost, Location $location): ConditionResult;
}

<?php

namespace App\Services\GhostMatcherService\Rules\Interfaces;

use App\Models\Location;
use App\Services\GhostMatcherService\Rules\DTO\ConditionResult;


interface ConditionRuleInterface
{
    public static function key(): string;

    public static function label(): string;

    public function evaluate(Location $location): ConditionResult;
}

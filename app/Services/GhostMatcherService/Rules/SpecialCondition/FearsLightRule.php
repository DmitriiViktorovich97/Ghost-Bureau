<?php

namespace App\Services\GhostMatcherService\Rules\SpecialCondition;

use App\Models\Location;
use App\Services\GhostMatcherService\Rules\DTO\ConditionResult;
use App\Services\GhostMatcherService\Rules\Interfaces\ConditionRuleInterface;

class FearsLightRule implements ConditionRuleInterface
{
    public static function key(): string
    {
        return "fears_light";
    }

    public static function label(): string
    {
        return "Боится света";
    }

    public function evaluate(Location $location): ConditionResult
    {
        return match ($location->lighting) {
            "dark"   => ConditionResult::reason(15, "Темно (+15)"),
            "dim"    => ConditionResult::reason(5, "Полумрак (+5)"),
            "bright" => ConditionResult::warning(-30, "Слишком светло (-30)"),
        };
    }
}

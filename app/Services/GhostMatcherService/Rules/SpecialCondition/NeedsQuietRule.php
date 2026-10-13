<?php

namespace App\Services\GhostMatcherService\Rules\SpecialCondition;

use App\Models\Location;
use App\Services\GhostMatcherService\Rules\Interfaces\ConditionRuleInterface;
use App\Services\GhostMatcherService\Rules\DTO\ConditionResult;

class NeedsQuietRule implements ConditionRuleInterface
{
    public static function key(): string
    {
        return "needs_quiet";
    }

    public static function label(): string
    {
        return "Нужна тишина";
    }

    public function evaluate(Location $location): ConditionResult
    {
        return match ($location->noise_level)
        {
            "quiet"    => ConditionResult::reason(15, "Тихо (+15)"),
            "moderate" => ConditionResult::warning(-10, "Умеренный шум (-10)"),
            "loud"     => ConditionResult::warning(-30, "Шумно (-30)"),
        };
    }
}

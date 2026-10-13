<?php

namespace App\Services\GhostMatcherService\Rules\SpecialCondition;

use App\Models\Location;
use App\Services\GhostMatcherService\Rules\DTO\ConditionResult;
use App\Services\GhostMatcherService\Rules\Interfaces\ConditionRuleInterface;

class FearsMirrorsRuleInterface implements ConditionRuleInterface
{
    public static function key(): string
    {
        return "fears_mirrors";
    }

    public static function label(): string
    {
        return "Боится зеркал";
    }

    public function evaluate(Location $location): ConditionResult
    {
        return !$location->has_mirrors
            ? ConditionResult::reason(10, "Зеркал нет (+10)")
            : ConditionResult::blocker("Боится зеркал — в месте есть зеркала");
    }
}

<?php

namespace App\Services\GhostMatcherService\Rules\SpecialCondition;

use App\Models\Location;
use App\Services\GhostMatcherService\Rules\DTO\ConditionResult;
use App\Services\GhostMatcherService\Rules\Interfaces\ConditionRuleInterface;

class NeedsAtticRuleInterface implements ConditionRuleInterface
{
    public static function key(): string
    {
        return "needs_attic";
    }

    public static function label(): string
    {
        return "Нужен чердак";
    }

    public function evaluate(Location $location): ConditionResult
    {
        return $location->has_attic
            ? ConditionResult::reason(20, "Есть чердак (+20)")
            : ConditionResult::blocker("Нужен чердак — его нет");
    }
}

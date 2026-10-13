<?php

namespace App\Services\GhostMatcherService\Rules\SpecialCondition;

use App\Models\Location;
use App\Services\GhostMatcherService\Rules\DTO\ConditionResult;
use App\Services\GhostMatcherService\Rules\Interfaces\ConditionRuleInterface;

class LikesDampRuleInterface implements ConditionRuleInterface
{
    public static function key(): string
    {
        return "likes_damp";
    }

    public static function label(): string
    {
        return "Любит сырость";
    }

    public function evaluate(Location $location): ConditionResult
    {
        return $location->humidity === "damp"
            ? ConditionResult::reason(15, "Сыро, как любит (+15)")
            : ConditionResult::warning(-25, "Недостаточно сыро (-25)");
    }
}

<?php

namespace App\Services\GhostMatcherService\Rules\SpecialCondition;

use App\Models\Location;
use App\Services\GhostMatcherService\Rules\DTO\ConditionResult;
use App\Services\GhostMatcherService\Rules\Interfaces\ConditionRuleInterface;

class NoHumansNearbyRuleInterface implements ConditionRuleInterface
{
    public static function key(): string
    {
        return 'no_humans_nearby';
    }

    public static function label(): string
    {
        return 'Нельзя рядом с людьми';
    }

    public function evaluate(Location $location): ConditionResult
    {
        return !$location->has_people
            ? ConditionResult::reason(15, 'Людей нет (+15)')
            : ConditionResult::blocker('Нельзя рядом с людьми — в месте есть люди');
    }
}

<?php

namespace App\Services\GhostMatcherService\Rules\Extra;

use App\Models\Ghost;
use App\Models\Location;
use App\Services\GhostMatcherService\Rules\DTO\ConditionResult;
use App\Services\GhostMatcherService\Rules\Interfaces\ExtraRuleInterface;

class AnxietyRule implements ExtraRuleInterface
{
    public function evaluate(Ghost $ghost, Location $location): ConditionResult
    {
        if ($ghost->anxiety_level < 7)
            return ConditionResult::neutral();

        if ($location->noise_level === 'loud')
            return ConditionResult::warning(-20, 'Шумно для тревожного привидения (-20)');

        if ($location->noise_level === 'quiet' && $location->lighting === 'dark')
            return ConditionResult::reason(10, 'Спокойное место для тревожного привидения (+10)');

        return ConditionResult::neutral();
    }
}

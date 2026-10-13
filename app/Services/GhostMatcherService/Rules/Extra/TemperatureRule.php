<?php

namespace App\Services\GhostMatcherService\Rules\Extra;

use App\Models\Ghost;
use App\Models\Location;
use App\Services\GhostMatcherService\Rules\DTO\ConditionResult;
use App\Services\GhostMatcherService\Rules\Interfaces\ExtraRuleInterface;

class TemperatureRule implements ExtraRuleInterface
{
    public function evaluate(Ghost $ghost, Location $location): ConditionResult
    {
        return match ($ghost->favorite_temperature)
        {
            'cold' => match ($location->humidity) {
                'damp'   => ConditionResult::reason(10, 'Прохладно и сыро — идеально (+10)'),
                'dry'    => ConditionResult::warning(-15, 'Сухо для холодного привидения (-15)'),
                default  => ConditionResult::neutral(),
            },
            'warm' => match ($location->humidity) {
                'dry'    => ConditionResult::reason(10, 'Сухо и тепло — идеально (+10)'),
                'damp'   => ConditionResult::warning(-15, 'Сыро для тёплого привидения (-15)'),
                default  => ConditionResult::neutral(),
            },
            'neutral' => $location->humidity === 'normal'
                ? ConditionResult::reason(5, 'Нейтральный климат (+5)')
                : ConditionResult::neutral(),
            default => ConditionResult::neutral(),
        };
    }
}

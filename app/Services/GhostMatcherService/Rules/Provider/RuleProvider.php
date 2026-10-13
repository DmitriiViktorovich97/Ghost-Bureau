<?php

namespace App\Services\GhostMatcherService\Rules\Provider;

use App\Services\GhostMatcherService\Rules\Extra\AnxietyRule;
use App\Services\GhostMatcherService\Rules\Extra\TemperatureRule;
use App\Services\GhostMatcherService\Rules\Interfaces\ConditionRuleInterface;
use App\Services\GhostMatcherService\Rules\SpecialCondition\FearsLightRule;
use App\Services\GhostMatcherService\Rules\SpecialCondition\FearsMirrorsRuleInterface;
use App\Services\GhostMatcherService\Rules\SpecialCondition\LikesDampRuleInterface;
use App\Services\GhostMatcherService\Rules\SpecialCondition\NeedsAtticRuleInterface;
use App\Services\GhostMatcherService\Rules\SpecialCondition\NeedsQuietRule;
use App\Services\GhostMatcherService\Rules\SpecialCondition\NoHumansNearbyRuleInterface;

class RuleProvider
{
    private static ?array $conditionInstances = null;
    private static ?array $extraInstances     = null;

    private const array CONDITION_RULES = [
        NeedsAtticRuleInterface::class,
        FearsMirrorsRuleInterface::class,
        NoHumansNearbyRuleInterface::class,
        LikesDampRuleInterface::class,
        FearsLightRule::class,
        NeedsQuietRule::class,
    ];

    private const array EXTRA_RULES = [
        AnxietyRule::class,
        TemperatureRule::class,
    ];

    private static function conditionInstances(): array
    {
        return self::$conditionInstances ??= array_map(fn($c) => new $c(), self::CONDITION_RULES);
    }

    private static function extraInstances(): array
    {
        return self::$extraInstances ??= array_map(fn($c) => new $c(), self::EXTRA_RULES);
    }

    public static function find(string $key): ?ConditionRuleInterface
    {
        foreach (self::conditionInstances() as $rule)
            if ($rule::key() === $key) return $rule;
        return null;
    }

    public static function options(): array
    {
        $out = [];
        foreach (self::conditionInstances() as $rule)
            $out[$rule::key()] = $rule::label();
        return $out;
    }

    public static function extras(): array
    {
        return self::extraInstances();
    }
}

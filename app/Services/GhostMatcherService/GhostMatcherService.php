<?php

namespace App\Services\GhostMatcherService;

use App\Models\Ghost;
use App\Models\Location;
use App\Services\GhostMatcherService\Rules\DTO\ConditionResult;
use App\Services\GhostMatcherService\Rules\Provider\RuleProvider;

class GhostMatcherService
{
    public const EXCELLENT = 100;
    public const GOOD = 70;
    public const WEAK = 40;

    public function score(Ghost $ghost, Location $location): array
    {
        $score   = 100;
        $reasons = $warnings = $blockers = [];

        // Вместимость
        $occupied = $location->ghosts()->count();
        if ($ghost->location_id === $location->id)
            $occupied = max(0, $occupied - 1);

        if ($occupied >= $location->capacity)
            $blockers[] = "Место переполнено ({$occupied}/{$location->capacity})";

        // Дедлайн
        if ($ghost->isExpired())
            $blockers[] = "Дедлайн просрочен (" . $ghost->deadline->format("d.m.Y") . ")";

        // Особые условия
        foreach ($ghost->special_conditions ?? [] as $cond)
        {
            $e = $ghost->evaluateCondition($cond, $location);
            $score += $e->score;

            match ($e->type) {
                ConditionResult::TYPE_REASON  => $reasons[]  = $e->text,
                ConditionResult::TYPE_WARNING => $warnings[] = $e->text,
                ConditionResult::TYPE_BLOCKER => $blockers[] = $e->text,
                default => null,
            };
        }

        // Тревожность и температура
        foreach (RuleProvider::extras() as $rule) {
            $r = $rule->evaluate($ghost, $location);
            $score += $r->score;
            match ($r->type) {
                ConditionResult::TYPE_REASON  => $reasons[]  = $r->text,
                ConditionResult::TYPE_WARNING => $warnings[] = $r->text,
                ConditionResult::TYPE_BLOCKER => $blockers[] = $r->text,
                default => null,
            };
        }

        $score = max(-100, min(150, $score));

        return [
            "score"    => $score,
            "reasons"  => $reasons,
            "warnings" => $warnings,
            "blockers" => $blockers,
            "possible" => empty($blockers),
        ];
    }

    private function temperatureDelta(string $temp, string $humidity): array
    {
        return match ($temp) {
            "cold" => match ($humidity) {
                "damp" => [10,
                    "Прохладно и сыро — идеально (+10)"],
                "normal" => [0,
                    ""],
                "dry" => [-15,
                    "Сухо для холодного привидения (-15)"],
            },
            "warm" => match ($humidity) {
                "dry" => [10,
                    "Сухо и тепло — идеально (+10)"],
                "normal" => [0,
                    ""],
                "damp" => [-15,
                    "Сыро для тёплого привидения (-15)"],
            },
            "neutral" => match ($humidity) {
                "normal" => [5,
                    "Нейтральный климат (+5)"],
                default => [0,
                    ""],
            },
            default => [0,
                ""],
        };
    }

    public function findBest(Ghost $ghost): ?array
    {
        $best = null;
        foreach (Location::all() as $location) {
            $r = $this->score($ghost, $location);
            if (!$r["possible"]) continue;
            if ($best === null || $r["score"] > $best["result"]["score"]) {
                $best = ["location" => $location,
                    "result" => $r];
            }
        }
        return $best;
    }

    public function explain(Ghost $ghost): array
    {
        $candidates = [];
        foreach (Location::all() as $location) {
            $candidates[] = ["location" => $location,
                "result" => $this->score($ghost, $location)];
        }
        usort($candidates, fn($a, $b) => $b["result"]["score"] <=> $a["result"]["score"]);
        return $candidates;
    }
}

<?php

namespace App\Models;

use App\Services\GhostMatcherService\Rules\DTO\ConditionResult;
use App\Services\GhostMatcherService\Rules\Provider\RuleProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ghost extends Model
{
    protected $fillable = [
        "name", "anxiety_level", "favorite_temperature", "deadline",
        "special_conditions", "location_id", "match_score", "match_reason",
    ];
    protected $casts = [
        "special_conditions" => "array",
        "deadline" => "date",
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function isExpired(): bool {
        return $this->deadline->isPast();
    }

    public function evaluateCondition(string $key, Location $location): ConditionResult
    {
        $rule = RuleProvider::find($key);
        return $rule?->evaluate($location) ?? ConditionResult::neutral();
    }

    public static function conditionLabels(): array
    {
        static $cache = null;
        return $cache ??= RuleProvider::options();
    }

    public function conditionLabel(string $key): string
    {
        return self::conditionLabels()[$key] ?? $key;
    }
}

<?php
namespace App\Services\GhostMatcherService\Rules\DTO;

class ConditionResult
{
    public const string TYPE_REASON  = 'reason';
    public const string TYPE_WARNING = 'warning';
    public const string TYPE_BLOCKER = 'blocker';

    public function __construct(
        public readonly int     $score,
        public readonly ?string $type,
        public readonly ?string $text,
    ) {}

    public static function reason(int $score, string $text): self
    {
        return new self($score, self::TYPE_REASON, $text);
    }

    public static function warning(int $score, string $text): self
    {
        return new self($score, self::TYPE_WARNING, $text);
    }

    public static function blocker(string $text): self
    {
        return new self(0, self::TYPE_BLOCKER, $text);
    }

    public static function neutral(): self
    {
        return new self(0, null, null);
    }
}

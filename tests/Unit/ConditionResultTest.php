<?php

use App\Services\GhostMatcherService\Rules\DTO\ConditionResult;
use Tests\TestCase;

class ConditionResultTest extends TestCase
{
    public function test_reason_factory(): void
    {
        $r = ConditionResult::reason(15, 'Есть чердак (+15)');
        $this->assertSame(15, $r->score);
        $this->assertSame(ConditionResult::TYPE_REASON, $r->type);
        $this->assertSame('Есть чердак (+15)', $r->text);
    }

    public function test_warning_factory(): void
    {
        $r = ConditionResult::warning(-25, 'Сухо (-25)');
        $this->assertSame(-25, $r->score);
        $this->assertSame(ConditionResult::TYPE_WARNING, $r->type);
    }

    public function test_blocker_factory(): void
    {
        $r = ConditionResult::blocker('Нужен чердак');
        $this->assertSame(0, $r->score);
        $this->assertSame(ConditionResult::TYPE_BLOCKER, $r->type);
        $this->assertSame('Нужен чердак', $r->text);
    }

    public function test_neutral_factory(): void
    {
        $r = ConditionResult::neutral();
        $this->assertSame(0, $r->score);
        $this->assertNull($r->type);
        $this->assertNull($r->text);
    }
}

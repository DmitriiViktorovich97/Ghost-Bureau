<?php

use App\Services\GhostMatcherService\Rules\Interfaces\ConditionRuleInterface;
use App\Services\GhostMatcherService\Rules\Provider\RuleProvider;
use Tests\TestCase;

class RuleProviderTest extends TestCase
{
    public function test_find_returns_rule_by_key(): void
    {
        $rule = RuleProvider::find('needs_attic');
        $this->assertInstanceOf(ConditionRuleInterface::class, $rule);
        $this->assertSame('needs_attic', $rule::key());
    }

    public function test_find_returns_null_for_unknown_key(): void
    {
        $this->assertNull(RuleProvider::find('does_not_exist'));
    }

    public function test_options_contains_all_six_labels(): void
    {
        $options = RuleProvider::options();
        $this->assertCount(6, $options);
        $this->assertArrayHasKey('needs_attic', $options);
        $this->assertArrayHasKey('fears_mirrors', $options);
        $this->assertArrayHasKey('no_humans_nearby', $options);
        $this->assertArrayHasKey('likes_damp', $options);
        $this->assertArrayHasKey('fears_light', $options);
        $this->assertArrayHasKey('needs_quiet', $options);
    }

    public function test_extras_returns_two_rules(): void
    {
        $this->assertCount(2, RuleProvider::extras());
    }
}

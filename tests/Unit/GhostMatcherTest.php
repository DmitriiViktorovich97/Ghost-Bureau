<?php

use App\Models\Ghost;
use App\Models\Location;
use App\Services\GhostMatcherService\GhostMatcherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GhostMatcherTest extends TestCase
{
    use RefreshDatabase;

    private GhostMatcherService $matcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->matcher = app(GhostMatcherService::class);
    }

    private function makeLocation(array $o = []): Location
    {
        return Location::create(array_merge([
            'name' => 'Тестовое место',
            'type' => 'test',
            'capacity' => 3,
            'lighting' => 'dark',
            'noise_level' => 'quiet',
            'humidity' => 'damp',
            'has_people' => false,
            'has_attic' => false,
            'has_mirrors' => false,
        ], $o));
    }

    private function makeGhost(array $o = []): Ghost
    {
        return Ghost::create(array_merge([
            'name' => 'Тестовый призрак',
            'anxiety_level' => 5,
            'favorite_temperature' => 'cold',
            'deadline' => now()->addDays(10),
            'special_conditions' => [],
        ], $o));
    }

    public function test_full_location_blocks_assignment(): void
    {
        $loc = $this->makeLocation(['capacity' => 1]);
        $this->makeGhost(['location_id' => $loc->id]);
        $ghost = $this->makeGhost();

        $r = $this->matcher->score($ghost, $loc);

        $this->assertFalse($r['possible']);
        $this->assertNotEmpty($r['blockers']);
        $this->assertStringContainsString('переполнено', $r['blockers'][0]);
    }

    public function test_expired_deadline_is_blocker(): void
    {
        $loc = $this->makeLocation();
        $ghost = $this->makeGhost(['deadline' => now()->subDay()]);

        $r = $this->matcher->score($ghost, $loc);

        $this->assertFalse($r['possible']);
        $this->assertStringContainsString('Дедлайн просрочен', $r['blockers'][0]);
    }

    public function test_fears_mirrors_blocks_location_with_mirrors(): void
    {
        $loc = $this->makeLocation(['has_mirrors' => true]);
        $ghost = $this->makeGhost(['special_conditions' => ['fears_mirrors']]);

        $r = $this->matcher->score($ghost, $loc);

        $this->assertFalse($r['possible']);
        $this->assertStringContainsString('зеркал', $r['blockers'][0]);
    }

    public function test_needs_attic_blocks_location_without_attic(): void
    {
        $loc = $this->makeLocation(['has_attic' => false]);
        $ghost = $this->makeGhost(['special_conditions' => ['needs_attic']]);

        $r = $this->matcher->score($ghost, $loc);

        $this->assertFalse($r['possible']);
        $this->assertStringContainsString('чердак', $r['blockers'][0]);
    }

    public function test_perfect_match_scores_above_100(): void
    {
        $loc = $this->makeLocation([
            'has_attic' => true,
            'has_mirrors' => false,
            'has_people' => false,
            'humidity' => 'damp',
        ]);
        $ghost = $this->makeGhost([
            'favorite_temperature' => 'cold',
            'special_conditions' => ['needs_attic', 'fears_mirrors', 'no_humans_nearby', 'likes_damp'],
        ]);

        $r = $this->matcher->score($ghost, $loc);

        $this->assertTrue($r['possible']);
        $this->assertGreaterThan(100, $r['score']);
        $this->assertNotEmpty($r['reasons']);
    }

    public function test_temperature_vs_humidity_affects_score(): void
    {
        $locDamp = $this->makeLocation(['humidity' => 'damp']);
        $locDry  = $this->makeLocation(['humidity' => 'dry', 'name' => 'Сухое']);
        $ghost = $this->makeGhost(['favorite_temperature' => 'cold']);

        $dampScore = $this->matcher->score($ghost, $locDamp)['score'];
        $dryScore  = $this->matcher->score($ghost, $locDry)['score'];

        $this->assertGreaterThan($dryScore, $dampScore);
    }

    public function test_find_best_returns_null_when_no_match(): void
    {
        $this->makeLocation(['capacity' => 1, 'has_attic' => false, 'has_mirrors' => true]);
        $ghost = $this->makeGhost(['special_conditions' => ['needs_attic', 'fears_mirrors']]);

        $this->assertNull($this->matcher->findBest($ghost));
    }

    public function test_ghost_does_not_count_itself_against_capacity(): void
    {
        $loc = $this->makeLocation(['capacity' => 1]);
        $ghost = $this->makeGhost(['location_id' => $loc->id]);

        $r = $this->matcher->score($ghost, $loc);

        $this->assertTrue($r['possible'], 'Сам себя призрак не должен считать за занятое место');
    }
}

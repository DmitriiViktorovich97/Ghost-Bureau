<?php

use App\Models\Ghost;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    private function makeLocation(int $capacity = 3): Location
    {
        return Location::query()->create([
            'name' => 'Место',
            'type' => 'test',
            'capacity' => $capacity,
            'lighting' => 'dark',
            'noise_level' => 'quiet',
            'humidity' => 'damp',
            'has_people' => false,
            'has_attic' => false,
            'has_mirrors' => false,
        ]);
    }

    private function makeGhost(int $locationId): Ghost
    {
        return Ghost::query()->create([
            'name' => 'Призрак',
            'anxiety_level' => 5,
            'favorite_temperature' => 'cold',
            'deadline' => now()->addDays(10),
            'special_conditions' => [],
            'location_id' => $locationId,
        ]);
    }

    public function test_occupied_counts_assigned_ghosts(): void
    {
        $loc = $this->makeLocation(3);
        $this->makeGhost($loc->id);
        $this->makeGhost($loc->id);

        $this->assertSame(2, $loc->occupied());
    }

    public function test_free_returns_remaining_slots(): void
    {
        $loc = $this->makeLocation(3);
        $this->makeGhost($loc->id);

        $this->assertSame(2, $loc->free());
    }

    public function test_free_never_goes_below_zero(): void
    {
        $loc = $this->makeLocation(1);
        $this->makeGhost($loc->id);
        $this->makeGhost($loc->id);

        $this->assertSame(0, $loc->free());
    }

    public function test_is_full_when_capacity_reached(): void
    {
        $loc = $this->makeLocation(1);
        $this->makeGhost($loc->id);

        $this->assertTrue($loc->isFull());
    }

    public function test_is_not_full_when_slots_available(): void
    {
        $loc = $this->makeLocation(2);
        $this->makeGhost($loc->id);

        $this->assertFalse($loc->isFull());
    }
}

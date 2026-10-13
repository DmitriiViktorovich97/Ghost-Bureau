<?php

use App\Models\Ghost;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GhostControllerTest extends TestCase
{
    use RefreshDatabase;

    private function makeLocation(array $o = []): Location
    {
        return Location::query()->create(array_merge([
            'name' => 'Место',
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
        return Ghost::query()->create(array_merge([
            'name' => 'Призрак',
            'anxiety_level' => 5,
            'favorite_temperature' => 'cold',
            'deadline' => now()->addDays(10),
            'special_conditions' => [],
        ], $o));
    }

    public function test_empty_ghosts_page_shows_placeholder(): void
    {
        $response = $this->get('/ghosts');

        $response->assertOk();
        $response->assertSee('Пока нет ни одной заявки');
        $response->assertSee('Создать заявку');
    }

    public function test_auto_match_assigns_best_location(): void
    {
        $loc = $this->makeLocation(['has_attic' => true, 'has_mirrors' => false]);
        $ghost = $this->makeGhost(['special_conditions' => ['needs_attic']]);

        $response = $this->post("/ghosts/{$ghost->id}/auto");

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('ghosts', [
            'id' => $ghost->id,
            'location_id' => $loc->id,
        ]);
    }

    public function test_auto_match_reports_failure_when_no_location(): void
    {
        $this->makeLocation([
            'capacity' => 1,
            'has_attic' => false,
            'has_mirrors' => true,
            'has_people' => true,
        ]);
        $ghost = $this->makeGhost([
            'special_conditions' => ['needs_attic', 'fears_mirrors', 'no_humans_nearby'],
        ]);

        $response = $this->post("/ghosts/{$ghost->id}/auto");

        $response->assertRedirect();
        $response->assertSessionHas('warning');
        $this->assertDatabaseHas('ghosts', [
            'id' => $ghost->id,
            'location_id' => null,
        ]);
    }

    public function test_manual_assign_with_conflict_shows_error(): void
    {
        $loc = $this->makeLocation(['has_mirrors' => true]);
        $ghost = $this->makeGhost(['special_conditions' => ['fears_mirrors']]);

        $response = $this->post("/ghosts/{$ghost->id}/assign", [
            'location_id' => $loc->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_manual_assign_without_conflict_shows_success(): void
    {
        $loc = $this->makeLocation(['has_mirrors' => false]);
        $ghost = $this->makeGhost(['special_conditions' => ['fears_mirrors']]);

        $response = $this->post("/ghosts/{$ghost->id}/assign", [
            'location_id' => $loc->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('ghosts', [
            'id' => $ghost->id,
            'location_id' => $loc->id,
        ]);
    }

    public function test_unassign_clears_location(): void
    {
        $loc = $this->makeLocation();
        $ghost = $this->makeGhost(['location_id' => $loc->id]);

        $response = $this->post("/ghosts/{$ghost->id}/unassign");

        $response->assertRedirect();
        $this->assertDatabaseHas('ghosts', [
            'id' => $ghost->id,
            'location_id' => null,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->post('/ghosts', []);

        $response->assertSessionHasErrors(['name', 'anxiety_level', 'favorite_temperature', 'deadline']);
    }

    public function test_store_rejects_invalid_condition(): void
    {
        $response = $this->post('/ghosts', [
            'name' => 'Тест',
            'anxiety_level' => 5,
            'favorite_temperature' => 'cold',
            'deadline' => now()->addDay()->toDateString(),
            'special_conditions' => ['fake_condition'],
        ]);

        $response->assertSessionHasErrors('special_conditions.0');
    }
}

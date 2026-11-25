<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MapRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_returns_map_for_valid_seed(): void
    {
        $response = $this->getJson('/api/maps/42');

        $response->assertOk()
            ->assertJsonPath('seed', 42)
            ->assertJsonPath('width', 40)
            ->assertJsonPath('height', 20)
            ->assertJsonCount(20, 'tiles')
            ->assertJsonCount(40, 'tiles.0');
    }

    public function test_api_rejects_non_numeric_seed(): void
    {
        $this->getJson('/api/maps/abc')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('seed');
    }

    public function test_api_rejects_seed_outside_int_range(): void
    {
        $this->getJson('/api/maps/99999999999999999999')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('seed');
    }

    public function test_api_rejects_invalid_size(): void
    {
        $this->getJson('/api/maps/42?width=0')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('width');

        $this->getJson('/api/maps/42?height=200')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('height');
    }

    public function test_same_seed_gives_same_tiles(): void
    {
        $first = $this->getJson('/api/maps/42')->json('tiles');
        $second = $this->getJson('/api/maps/42')->json('tiles');

        $this->assertSame($first, $second);
    }

    public function test_search_redirects_to_map_page(): void
    {
        $this->get('/maps/search?seed=42')
            ->assertRedirect(route('maps.show', [
                'seed' => 42,
                'x' => 0,
                'y' => 0,
                'width' => 40,
                'height' => 20,
            ]));
    }

    public function test_search_without_seed_returns_back_with_errors(): void
    {
        $this->from('/maps')
            ->get('/maps/search')
            ->assertRedirect('/maps')
            ->assertSessionHasErrors('seed');
    }

    public function test_index_page_has_seed_form(): void
    {
        $this->get('/maps')
            ->assertOk()
            ->assertSee('name="seed"', false);
    }

    public function test_web_page_shows_map_for_valid_seed(): void
    {
        $this->get('/maps/42')
            ->assertOk()
            ->assertSee('Карта, сид 42');
    }

    public function test_web_page_redirects_back_for_invalid_seed(): void
    {
        $this->from('/maps')
            ->get('/maps/abc')
            ->assertRedirect('/maps')
            ->assertSessionHasErrors('seed');
    }

    public function test_web_and_api_requests_are_counted_in_one_row(): void
    {
        $this->get('/maps/42');
        $this->getJson('/api/maps/42');

        $this->assertDatabaseCount('map_requests', 1);
        $this->assertDatabaseHas('map_requests', [
            'seed' => 42,
            'origin_x' => 0,
            'origin_y' => 0,
            'width' => 40,
            'height' => 20,
            'hits' => 2,
        ]);
    }

    public function test_invalid_requests_and_search_redirect_are_not_counted(): void
    {
        $this->getJson('/api/maps/abc');
        $this->from('/maps')->get('/maps/abc');
        $this->get('/maps/search?seed=42');

        $this->assertDatabaseCount('map_requests', 0);
    }
}
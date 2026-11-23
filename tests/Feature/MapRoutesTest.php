<?php

namespace Tests\Feature;

use Tests\TestCase;

final class MapRoutesTest extends TestCase
{
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
}
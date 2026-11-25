<?php

namespace Tests\Feature;

use App\Services\MapStats\MapStatsRecorder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MapStatsRecorderTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_request_creates_row_with_one_hit(): void
    {
        $hits = app(MapStatsRecorder::class)->record(42, 0, 0, 40, 20);

        $this->assertSame(1, $hits);
        $this->assertDatabaseCount('map_requests', 1);
        $this->assertDatabaseHas('map_requests', [
            'seed' => 42,
            'origin_x' => 0,
            'origin_y' => 0,
            'width' => 40,
            'height' => 20,
            'hits' => 1,
        ]);
    }

    public function test_repeated_request_increments_hits_without_new_row(): void
    {
        $recorder = app(MapStatsRecorder::class);

        $recorder->record(42, 0, 0, 40, 20);
        $hits = $recorder->record(42, 0, 0, 40, 20);

        $this->assertSame(2, $hits);
        $this->assertDatabaseCount('map_requests', 1);
        $this->assertDatabaseHas('map_requests', ['seed' => 42, 'hits' => 2]);
        $this->assertDatabaseMissing('map_requests', ['seed' => 42, 'last_requested_at' => null]);
    }

    public function test_different_origin_creates_new_row(): void
    {
        $recorder = app(MapStatsRecorder::class);

        $recorder->record(42, 0, 0, 40, 20);
        $hits = $recorder->record(42, 40, 0, 40, 20);

        $this->assertSame(1, $hits);
        $this->assertDatabaseCount('map_requests', 2);
    }

    public function test_negative_values_and_int_bounds_are_stored_correctly(): void
    {
        $recorder = app(MapStatsRecorder::class);

        $recorder->record(PHP_INT_MIN, PHP_INT_MIN, PHP_INT_MAX, 1, 100);
        $recorder->record(PHP_INT_MAX, -1, -1000000, 100, 1);

        $this->assertDatabaseCount('map_requests', 2);
        $this->assertDatabaseHas('map_requests', [
            'seed' => PHP_INT_MIN,
            'origin_x' => PHP_INT_MIN,
            'origin_y' => PHP_INT_MAX,
            'width' => 1,
            'height' => 100,
            'hits' => 1,
        ]);
        $this->assertDatabaseHas('map_requests', [
            'seed' => PHP_INT_MAX,
            'origin_x' => -1,
            'origin_y' => -1000000,
            'width' => 100,
            'height' => 1,
            'hits' => 1,
        ]);
    }
}
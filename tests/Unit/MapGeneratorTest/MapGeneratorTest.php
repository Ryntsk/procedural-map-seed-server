<?php

namespace Tests\Unit;

use App\Services\MapGen\MapGenerator;
use App\Services\MapGen\TileType;
use PHPUnit\Framework\TestCase;

final class MapGeneratorTest extends TestCase
{
    public function test_same_seed_produces_identical_map(): void
    {
        $a = (new MapGenerator(12345))->grid(0, 0, 20, 20);
        $b = (new MapGenerator(12345))->grid(0, 0, 20, 20);

        $this->assertSame($a, $b);
    }

    public function test_different_seeds_produce_different_maps(): void
    {
        $a = (new MapGenerator(1))->grid(0, 0, 20, 20);
        $b = (new MapGenerator(2))->grid(0, 0, 20, 20);

        $this->assertNotSame($a, $b);
    }

    public function test_chunks_are_consistent_regardless_of_generation_order(): void
    {
        $gen = new MapGenerator(777);

        $whole = $gen->grid(0, 0, 10, 10);
        $chunk = $gen->grid(5, 5, 3, 3);

        // Клетка (5, 5) из большой сетки должна совпасть с клеткой из отдельного чанка.
        $this->assertSame($whole[5][5], $chunk[0][0]);
        $this->assertSame($whole[7][7], $chunk[2][2]);
    }

    public function test_plain_is_the_most_common_tile(): void
    {
        $counts = array_fill_keys(array_map(fn (TileType $t) => $t->value, TileType::cases()), 0);

        foreach ((new MapGenerator(99))->grid(0, 0, 100, 100) as $row) {
            foreach ($row as $tile) {
                $counts[$tile->value]++;
            }
        }

        $this->assertGreaterThan($counts['water'], $counts['plain']);
        $this->assertGreaterThan($counts['forest'], $counts['plain']);
        $this->assertGreaterThan($counts['mountain'], $counts['plain']);
    }

    public function test_distribution_is_roughly_as_configured(): void
    {
        $counts = array_fill_keys(array_map(fn (TileType $t) => $t->value, TileType::cases()), 0);
        $total = 0;

        foreach ((new MapGenerator(42))->grid(0, 0, 200, 200) as $row) {
            foreach ($row as $tile) {
                $counts[$tile->value]++;
                $total++;
            }
        }

        // Допуск 2 процентных пункта на выборке 40 000 клеток.
        $this->assertEqualsWithDelta(0.15, $counts['water'] / $total, 0.02);
        $this->assertEqualsWithDelta(0.50, $counts['plain'] / $total, 0.02);
        $this->assertEqualsWithDelta(0.20, $counts['forest'] / $total, 0.02);
        $this->assertEqualsWithDelta(0.15, $counts['mountain'] / $total, 0.02);
    }
}

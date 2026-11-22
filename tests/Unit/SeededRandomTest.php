<?php

namespace Tests\Unit;

use App\Services\MapGen\SeededRandom;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class SeededRandomTest extends TestCase
{
    public function test_mix64_matches_reference_splitmix64_vector(): void
    {
        // Первый выход SplitMix64 при сиде 0 (эталон 0xE220A8397B1DCDAF).
        // Вход: состояние 0x9E3779B97F4A7C15 после прибавления гаммы.
        $this->assertSame(-0x1DDF57C684E23251, SeededRandom::mix64(-0x61C8864680B583EB));
    }

    public function test_same_seed_and_coordinates_give_same_value(): void
    {
        $a = new SeededRandom(42);
        $b = new SeededRandom(42);

        $this->assertSame($a->hashAt(3, -7), $b->hashAt(3, -7));
        $this->assertSame($a->unitAt(3, -7), $b->unitAt(3, -7));
    }

    public function test_neighbouring_cells_produce_distinct_values(): void
    {
        $rng = new SeededRandom(42);
        $values = [];

        for ($x = 0; $x < 10; $x++) {
            for ($y = 0; $y < 10; $y++) {
                $values[] = $rng->hashAt($x, $y);
            }
        }

        $this->assertCount(100, array_unique($values));
    }

    public function test_swapped_coordinates_differ(): void
    {
        $rng = new SeededRandom(1);

        $this->assertNotSame($rng->hashAt(1, 0), $rng->hashAt(0, 1));
    }

    public function test_different_seeds_differ(): void
    {
        $this->assertNotSame(
            (new SeededRandom(1))->hashAt(5, 5),
            (new SeededRandom(2))->hashAt(5, 5),
        );
    }

    public function test_unit_value_is_within_zero_and_one(): void
    {
        $rng = new SeededRandom(7);

        for ($x = 0; $x < 1000; $x++) {
            $f = $rng->unitAt($x, $x * 3);
            $this->assertGreaterThanOrEqual(0.0, $f);
            $this->assertLessThan(1.0, $f);
        }
    }

    public function test_int_range_is_inclusive_and_bounded(): void
    {
        $rng = new SeededRandom(7);

        for ($x = 0; $x < 1000; $x++) {
            $v = $rng->intAt($x, 0, -3, 4);
            $this->assertGreaterThanOrEqual(-3, $v);
            $this->assertLessThanOrEqual(4, $v);
        }
    }

    public function test_int_range_rejects_inverted_bounds(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new SeededRandom(1))->intAt(0, 0, 5, 2);
    }
}
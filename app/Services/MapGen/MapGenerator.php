<?php

namespace App\Services\MapGen;

use InvalidArgumentException;

/**
 * Генерирует тип тайла по координатам и сиду.
 * Одинаковый сид и координаты всегда дают одинаковый тайл.
 */
final class MapGenerator
{
    /**
     * Веса типов тайлов в порядке накопления. Сумма должна быть равна 1.0.
     * Значения в процентах: вода 15, равнина 50, лес 20, горы 15.
     */
    private const DISTRIBUTION = [
        'water' => 0.15,
        'plain' => 0.50,
        'forest' => 0.20,
        'mountain' => 0.15,
    ];

    private readonly SeededRandom $rng;

    public function __construct(int $seed)
    {
        $this->rng = new SeededRandom($seed);
        self::assertDistributionIsValid();
    }

    public function tileAt(int $x, int $y): TileType
    {
        $u = $this->rng->unitAt($x, $y);
        $cumulative = 0.0;

        foreach (self::DISTRIBUTION as $type => $weight) {
            $cumulative += $weight;
            if ($u < $cumulative) {
                return TileType::from($type);
            }
        }

        // Защита от погрешности float: если сумма чуть меньше 1.0.
        return TileType::Mountain;
    }

    /**
     * Сетка тайлов размером width x height, начиная с (originX, originY).
     * Возвращает массив строк, каждая строка — массив TileType.
     */
    public function grid(int $originX, int $originY, int $width, int $height): array
    {
        $rows = [];
        for ($y = 0; $y < $height; $y++) {
            $row = [];
            for ($x = 0; $x < $width; $x++) {
                $row[] = $this->tileAt($originX + $x, $originY + $y);
            }
            $rows[] = $row;
        }

        return $rows;
    }

    private static function assertDistributionIsValid(): void
    {
        $sum = array_sum(self::DISTRIBUTION);
        if (abs($sum - 1.0) > 1e-9) {
            throw new InvalidArgumentException("Сумма весов тайлов должна быть 1.0, получено $sum");
        }
    }
}

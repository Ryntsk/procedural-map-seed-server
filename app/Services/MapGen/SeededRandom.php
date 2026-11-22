<?php

namespace App\Services\MapGen;

use InvalidArgumentException;

/**
 * Собственный детерминированный генератор на основе SplitMix64.
 * Не использует mt_rand: результат для одного сида не зависит от версии PHP.
 *
 * Значение для клетки (x, y) вычисляется напрямую от сида и координат,
 * поэтому карту можно генерировать чанками в любом порядке.
 *
 * Константы записаны в знаковом виде: беззнаковые литералы больше PHP_INT_MAX
 * PHP превращает во float, что ломает вычисления.
 */
final class SeededRandom
{
    private const MIX1 = -0x40A7B892E31B1A47; // 0xBF58476D1CE4E5B9
    private const MIX2 = -0x6B2FB644ECCEEE15; // 0x94D049BB133111EB

    public function __construct(private readonly int $seed)
    {
    }

    public function getSeed(): int
    {
        return $this->seed;
    }

    /**
     * 64-битный хеш клетки (x, y).
     */
    public function hashAt(int $x, int $y): int
    {
        $h = self::mix64($this->seed);
        $h = self::mix64($h ^ $x);

        return self::mix64($h ^ $y);
    }

    /**
     * Значение в диапазоне [0, 1) с 53 битами точности.
     */
    public function unitAt(int $x, int $y): float
    {
        return self::shr($this->hashAt($x, $y), 11) / 9007199254740992.0; // 2^53
    }

    /**
     * Целое в диапазоне [min, max] включительно.
     */
    public function intAt(int $x, int $y, int $min, int $max): int
    {
        if ($max < $min) {
            throw new InvalidArgumentException("max ($max) меньше min ($min)");
        }

        $n = $max - $min + 1;

        return $min + (self::shr($this->hashAt($x, $y), 1) % $n);
    }

    /**
     * Финализатор SplitMix64.
     */
    public static function mix64(int $z): int
    {
        $z = self::mul64($z ^ self::shr($z, 30), self::MIX1);
        $z = self::mul64($z ^ self::shr($z, 27), self::MIX2);

        return $z ^ self::shr($z, 31);
    }

    /**
     * Логический (беззнаковый) сдвиг вправо для 64-битного int, 1 <= $n <= 63.
     */
    private static function shr(int $x, int $n): int
    {
        return ($x >> $n) & (PHP_INT_MAX >> ($n - 1));
    }

    /**
     * Умножение по модулю 2^64 по 16-битным частям,
     * чтобы промежуточные значения не выходили за пределы int.
     */
    private static function mul64(int $a, int $b): int
    {
        $a0 = $a & 0xFFFF;
        $a1 = ($a >> 16) & 0xFFFF;
        $a2 = ($a >> 32) & 0xFFFF;
        $a3 = ($a >> 48) & 0xFFFF;

        $b0 = $b & 0xFFFF;
        $b1 = ($b >> 16) & 0xFFFF;
        $b2 = ($b >> 32) & 0xFFFF;
        $b3 = ($b >> 48) & 0xFFFF;

        $c = $a0 * $b0;
        $r0 = $c & 0xFFFF;
        $carry = $c >> 16;

        $c = $a0 * $b1 + $a1 * $b0 + $carry;
        $r1 = $c & 0xFFFF;
        $carry = $c >> 16;

        $c = $a0 * $b2 + $a1 * $b1 + $a2 * $b0 + $carry;
        $r2 = $c & 0xFFFF;
        $carry = $c >> 16;

        $c = $a0 * $b3 + $a1 * $b2 + $a2 * $b1 + $a3 * $b0 + $carry;
        $r3 = $c & 0xFFFF;

        return ($r3 << 48) | ($r2 << 32) | ($r1 << 16) | $r0;
    }
}
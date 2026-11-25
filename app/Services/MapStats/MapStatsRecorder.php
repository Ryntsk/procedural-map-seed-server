<?php

namespace App\Services\MapStats;

use App\Models\MapQuery;
use Illuminate\Support\Facades\DB;

/**
 * считаем обращения к картам. Карты не хранятся: только счётчик запросов
 */
final class MapStatsRecorder
{
    /**
     * фиксирует обращение и возвращает общее число обращений к этой карте
     */
    public function record(int $seed, int $originX, int $originY, int $width, int $height): int
    {
        $key = [
            'seed' => $seed,
            'origin_x' => $originX,
            'origin_y' => $originY,
            'width' => $width,
            'height' => $height,
        ];
        $now = now();

        DB::transaction(function () use ($key, $now): void {
            // Новая строка создаётся с hits = 0, затем счётчик увеличивается один раз
            // Так первый запрос даёт hits = 1, а повтор не создаёт дубликат
            MapQuery::query()->insertOrIgnore([
                ...$key,
                'hits' => 0,
                'created_at' => $now,
                'last_requested_at' => null,
            ]);

            MapQuery::query()
                ->where($key)
                ->update([
                    'hits' => DB::raw('hits + 1'),
                    'last_requested_at' => $now,
                ]);
        });

        return (int) MapQuery::query()->where($key)->value('hits');
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MapParamsRequest;
use App\Services\MapGen\MapGenerator;
use App\Services\MapGen\TileType;
use App\Services\MapStats\MapStatsRecorder;
use Illuminate\Http\JsonResponse;

class MapApiController extends Controller
{
    public function show(MapParamsRequest $request, MapStatsRecorder $stats): JsonResponse
    {
        $map = $request->validatedMap();

        // Статистика пишется до генерации. Если позже добавят кэш, ставьте его после этой строки.
        $stats->record($map['seed'], $map['x'], $map['y'], $map['width'], $map['height']);

        $grid = (new MapGenerator($map['seed']))->grid(
            $map['x'],
            $map['y'],
            $map['width'],
            $map['height'],
        );

        return response()->json([
            'seed' => $map['seed'],
            'x' => $map['x'],
            'y' => $map['y'],
            'width' => $map['width'],
            'height' => $map['height'],
            'tiles' => array_map(
                fn (array $row) => array_map(fn (TileType $tile) => $tile->value, $row),
                $grid,
            ),
        ]);
    }
}
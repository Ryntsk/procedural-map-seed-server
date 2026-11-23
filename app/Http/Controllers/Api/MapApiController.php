<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MapParamsRequest;
use App\Services\MapGen\MapGenerator;
use App\Services\MapGen\TileType;
use Illuminate\Http\JsonResponse;

class MapApiController extends Controller
{
    public function show(MapParamsRequest $request): JsonResponse
    {
        $map = $request->validatedMap();

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
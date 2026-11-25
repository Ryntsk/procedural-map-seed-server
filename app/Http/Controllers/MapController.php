<?php

namespace App\Http\Controllers;

use App\Http\Requests\MapParamsRequest;
use App\Services\MapGen\MapGenerator;
use App\Services\MapStats\MapStatsRecorder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class MapController extends Controller
{
    public function index(): View
    {
        return view('maps.index');
    }

    public function search(MapParamsRequest $request): RedirectResponse
    {
        // HTML-форма не умеет подставлять сид в путь, поэтому перенаправляем на /maps/{seed}.
        // Перенаправление обращением к карте не считается: статистика пишется только в show().
        return redirect()->route('maps.show', $request->validatedMap());
    }

    public function show(MapParamsRequest $request, MapStatsRecorder $stats): View
    {
        $map = $request->validatedMap();

        // Считаем каждое обращение к карте, включая повторные.
        $stats->record($map['seed'], $map['x'], $map['y'], $map['width'], $map['height']);

        $grid = (new MapGenerator($map['seed']))->grid(
            $map['x'],
            $map['y'],
            $map['width'],
            $map['height'],
        );

        return view('maps.show', ['map' => $map, 'grid' => $grid]);
    }
}
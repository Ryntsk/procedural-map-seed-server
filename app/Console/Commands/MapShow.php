<?php

namespace App\Console\Commands;

use App\Services\MapGen\MapGenerator;
use App\Services\MapGen\TileType;
use Illuminate\Console\Command;

class MapShow extends Command
{
    protected $signature = 'map:show
                            {seed : Сид карты}
                            {--x=0 : Начальная координата X}
                            {--y=0 : Начальная координата Y}
                            {--width=40 : Ширина в клетках}
                            {--height=20 : Высота в клетках}';

    protected $description = 'Выводит карту по сиду в консоль';

    private const SYMBOLS = [
        'water' => '~',
        'plain' => '.',
        'forest' => 'T',
        'mountain' => '^',
    ];

    public function handle(): int
    {
        $seed = (int) $this->argument('seed');
        $generator = new MapGenerator($seed);

        $grid = $generator->grid(
            (int) $this->option('x'),
            (int) $this->option('y'),
            (int) $this->option('width'),
            (int) $this->option('height'),
        );

        foreach ($grid as $row) {
            $line = '';
            foreach ($row as $tile) {
                $line .= self::SYMBOLS[$tile->value];
            }
            $this->line($line);
        }

        $this->newLine();
        $this->info("Сид: $seed, координаты ({$this->option('x')}, {$this->option('y')})");

        return self::SUCCESS;
    }
}
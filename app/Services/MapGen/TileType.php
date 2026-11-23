<?php

namespace App\Services\MapGen;

enum TileType: string
{
    case Water = 'water';       // непроходимая поверхность или водоёмы
    case Plain = 'plain';       // базовый проходимый тайл
    case Forest = 'forest';     // проходимый, с усложнением движения или укрытием
    case Mountain = 'mountain'; // препятствие или возвышенность
}

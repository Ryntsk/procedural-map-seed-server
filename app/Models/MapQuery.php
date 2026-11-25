<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['seed', 'origin_x', 'origin_y', 'width', 'height', 'hits', 'created_at', 'last_requested_at'])]
class MapQuery extends Model
{
    /**
     * Таблица статистики обращений к картам.
     */
    protected $table = 'map_requests';

    
    public $timestamps = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'seed' => 'integer',
            'origin_x' => 'integer',
            'origin_y' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'hits' => 'integer',
            'created_at' => 'datetime',
            'last_requested_at' => 'datetime',
        ];
    }
}
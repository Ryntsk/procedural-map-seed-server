<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MapParamsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Сид из пути (/maps/{seed}, /api/maps/{seed}); в поиске он приходит из query.
        if ($this->route('seed') !== null) {
            $this->merge(['seed' => $this->route('seed')]);
        }
    }

    public function rules(): array
    {
        return [
            'seed' => ['required', 'integer'],
            'x' => ['sometimes', 'integer'],
            'y' => ['sometimes', 'integer'],
            'width' => ['sometimes', 'integer', 'between:1,100'],
            'height' => ['sometimes', 'integer', 'between:1,100'],
        ];
    }

    /**
     * @return array{seed: int, x: int, y: int, width: int, height: int}
     */
    public function validatedMap(): array
    {
        $data = $this->validated();

        return [
            'seed' => (int) $data['seed'],
            'x' => (int) ($data['x'] ?? 0),
            'y' => (int) ($data['y'] ?? 0),
            'width' => (int) ($data['width'] ?? 40),
            'height' => (int) ($data['height'] ?? 20),
        ];
    }
}
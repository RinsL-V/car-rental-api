<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailableCarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'plate_number' => $this->plate_number,
            'model' => $this->model->name,
            'category' => [
                'name' => $this->model->category->name,

            ],
            'driver' => $this->driver ? [
                'id' => $this->driver->id,
                'full_name' => $this->driver->full_name,
                'license_number' => $this->driver->license_number,
            ] : null,
            'is_available' => $this->is_available,
            'next_maintenance' => $this->next_maintenance?->format('Y-m-d'),
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Car extends Model
{
    use SoftDeletes;

    protected $fillable = ['plate_number', 'car_model_id', 'driver_id', 'is_available'];

    protected $casts = ['is_available' => 'boolean'];

    public function model(): BelongsTo
    {
        return $this->belongsTo(CarModel::class, 'car_model_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function isAvailableBetween($start, $end): bool
    {
        if (!$start instanceof \Carbon\Carbon) {
            $start = \Carbon\Carbon::parse($start);
        }
        if (!$end instanceof \Carbon\Carbon) {
            $end = \Carbon\Carbon::parse($end);
        }

        return !$this->trips()
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_at', [$start, $end])
                    ->orWhereBetween('end_at', [$start, $end])
                    ->orWhere(fn($q) => $q
                        ->where('start_at', '<=', $start)
                        ->where('end_at', '>=', $end)
                    );
            })
            ->where('status', '!=', 'cancelled')
            ->exists();
    }

    public function getComfortLevelAttribute(): int
    {
        return $this->model->category->comfort_level;
    }
}

<?php

namespace App\Services;

use App\Models\Car;
use App\Models\User;
use Illuminate\Support\Collection;

class CarAvailabilityService
{
    /**
     * Получить доступные автомобили для пользователя
     */
    public function getAvailableCars(User $user, array $filters): Collection
    {
        // ID доступных категорий
        $availableCategoryIds = $user->getAvailableCategoryIds();

        if (empty($availableCategoryIds)) {
            return collect();
        }

        // Строим базовый запрос
        $query = Car::query()
            ->with(['model.category', 'driver'])
            ->where('is_available', true)
            ->whereHas('model.category', function ($query) use ($availableCategoryIds) {
                $query->whereIn('car_categories.id', $availableCategoryIds);
            });

        // Применяем фильтры
        $this->applyFilters($query, $filters);

        // Проверяем доступность по времени
        $this->checkAvailability($query, $filters['start_at'], $filters['end_at']);

        return $query->orderBy('car_models.car_category_id')->get();
    }

    /**
     * Применить фильтры к запросу
     */
    private function applyFilters($query, array $filters): void
    {
        // Фильтр по модели
        if (!empty($filters['car_model'])) {
            $query->whereHas('model', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['car_model'] . '%');
            });
        }

        // Фильтр по уровню комфорта
        if (!empty($filters['comfort_level'])) {
            $query->whereHas('model.category', function ($q) use ($filters) {
                $q->where('comfort_level', $filters['comfort_level']);
            });
        }
    }

    /**
     * Проверить доступность автомобилей в указанный период
     */
    private function checkAvailability($query, string $startAt, string $endAt): void
    {
        $query->whereDoesntHave('trips', function ($q) use ($startAt, $endAt) {
            $q->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($startAt, $endAt) {
                    $query->whereBetween('start_at', [$startAt, $endAt])
                        ->orWhereBetween('end_at', [$startAt, $endAt])
                        ->orWhere(function ($q) use ($startAt, $endAt) {
                            $q->where('start_at', '<=', $startAt)
                                ->where('end_at', '>=', $endAt);
                        });
                });
        });
    }
}

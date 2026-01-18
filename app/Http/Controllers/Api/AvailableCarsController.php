<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AvailableCarsRequest;
use App\Http\Resources\AvailableCarResource;
use App\Services\CarAvailabilityService;
use Illuminate\Http\JsonResponse;

class AvailableCarsController extends Controller
{
    public function __construct(
        private CarAvailabilityService $service
    ) {}

    /**
     * Получить список доступных автомобилей для текущего пользователя
     *
     * @param AvailableCarsRequest $request
     * @return JsonResponse
     *
     * Параметры:
     * - start_at (required): Дата начала поездки (Y-m-d H:i:s)
     * - end_at (required): Дата окончания поездки (Y-m-d H:i:s)
     * - car_model (optional): Фильтр по модели автомобиля
     * - comfort_level (optional): Фильтр по уровню комфорта (1-10)
     */
    public function __invoke(AvailableCarsRequest $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->position) {
            return response()->json([
                'success' => false,
                'message' => 'У вашей должности нет доступа к служебным автомобилям',
            ], 403);
        }

        $cars = $this->service->getAvailableCars($user, $request->validated());

        return response()->json([
            'success' => true,
            'data' => AvailableCarResource::collection($cars),
            'meta' => [
                'total' => $cars->count(),
                'user_position' => $user->position->title,
                'available_categories' => $user->getAvailableCategoryIds(),
            ],
        ]);
    }
}

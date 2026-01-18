<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class AvailableCarsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_at' => [
                'required',
                'date_format:Y-m-d H:i:s',
            ],
            'end_at' => [
                'required',
                'date_format:Y-m-d H:i:s',
            ],
            'car_model' => 'nullable|string|min:2|max:100',
            'comfort_level' => 'nullable|integer|min:1|max:10',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $startAt = $this->input('start_at');
            $endAt = $this->input('end_at');

            if (!$startAt || !$endAt) {
                return;
            }

            try {
                $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $startAt);
                $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $endAt);
                $now = Carbon::now();
                $maxStartDate = Carbon::now()->addDays(30);
                $maxEndDate = Carbon::now()->addDays(31);

                // Проверка start_at
                if ($startDate->isPast()) {
                    $validator->errors()->add('start_at', 'Дата начала не может быть в прошлом');
                }

                if ($startDate->gt($maxStartDate)) {
                    $validator->errors()->add('start_at', 'Дата начала не может быть более чем через 30 дней');
                }

                // Проверка end_at
                if ($endDate->lte($startDate)) {
                    $validator->errors()->add('end_at', 'Дата окончания должна быть после даты начала');
                }

                if ($endDate->gt($maxEndDate)) {
                    $validator->errors()->add('end_at', 'Дата окончания не может быть более чем через 31 день');
                }

            } catch (\Exception $e) {
                $validator->errors()->add('start_at', 'Неверный формат даты');
            }
        });
    }

    public function messages(): array
    {
        return [
            'start_at.required' => 'Дата начала обязательна',
            'start_at.date_format' => 'Неверный формат даты начала. Используйте: ГГГГ-ММ-ДД ЧЧ:ММ:СС',
            'end_at.required' => 'Дата окончания обязательна',
            'end_at.date_format' => 'Неверный формат даты окончания. Используйте: ГГГГ-ММ-ДД ЧЧ:ММ:СС',
        ];
    }
}

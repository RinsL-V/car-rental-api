<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->unique()->comment('Государственный номер');
            $table->foreignId('car_model_id')
                ->constrained()
                ->cascadeOnDelete()
                ->comment('Модель автомобиля');
            $table->foreignId('driver_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->comment('Закрепленный водитель');
            $table->boolean('is_available')->default(true)->comment('Доступен для бронирования');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_available', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};

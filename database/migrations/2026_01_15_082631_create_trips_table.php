<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete()
                ->comment('Сотрудник');
            $table->foreignId('car_id')
                ->constrained()
                ->cascadeOnDelete()
                ->comment('Автомобиль');
            $table->timestamp('start_at')->comment('Начало поездки');
            $table->timestamp('end_at')->comment('Окончание поездки');
            $table->string('purpose')->nullable()->comment('Цель поездки');
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])
                ->default('planned')
                ->comment('Статус поездки');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['start_at', 'end_at']);
            $table->index(['car_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};

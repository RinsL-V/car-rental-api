<?php

namespace Database\Seeders;

use App\Models\{
    CarCategory, CarModel, Car, Driver, Position, User, Trip
};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Категории
        $categories = [
            ['name' => 'Эконом', 'comfort_level' => 1],
            ['name' => 'Комфорт', 'comfort_level' => 2],
            ['name' => 'Бизнес', 'comfort_level' => 3],
            ['name' => 'Премиум', 'comfort_level' => 4],
        ];
        CarCategory::insert($categories);

        // Модели
        CarModel::insert([
            ['name' => 'Kia Rio', 'car_category_id' => 1],
            ['name' => 'Hyundai Solaris', 'car_category_id' => 1],
            ['name' => 'Toyota Camry', 'car_category_id' => 2],
            ['name' => 'Skoda Octavia', 'car_category_id' => 2],
            ['name' => 'BMW 5 Series', 'car_category_id' => 3],
            ['name' => 'Mercedes E-Class', 'car_category_id' => 3],
        ]);

        // Водители
        Driver::insert([
            ['full_name' => 'Иванов Иван', 'license_number' => 'АБ123456', 'is_active' => true],
            ['full_name' => 'Петров Пётр', 'license_number' => 'ВГ789012', 'is_active' => true],
        ]);

        // Автомобили
        Car::insert([
            ['plate_number' => 'А001АА77', 'car_model_id' => 1, 'driver_id' => 1, 'is_available' => true],
            ['plate_number' => 'В002ВВ77', 'car_model_id' => 2, 'driver_id' => 2, 'is_available' => true],
            ['plate_number' => 'С003СС77', 'car_model_id' => 3, 'driver_id' => null, 'is_available' => true],
            ['plate_number' => 'Е004ЕЕ77', 'car_model_id' => 4, 'driver_id' => null, 'is_available' => true],
            ['plate_number' => 'К005КК77', 'car_model_id' => 5, 'driver_id' => null, 'is_available' => true],
            ['plate_number' => 'М006ММ77', 'car_model_id' => 6, 'driver_id' => null, 'is_available' => true],
        ]);

        // Должности
        $positions = [
            ['title' => 'Курьер', 'code' => 'courier'],
            ['title' => 'Менеджер', 'code' => 'manager'],
            ['title' => 'Директор', 'code' => 'director'],
        ];
        Position::insert($positions);

        // Привязка должностей к категориям
        $manager = Position::find(2);
        $manager->carCategories()->attach([1, 2]); // Эконом и Комфорт

        $director = Position::find(3);
        $director->carCategories()->attach([1, 2, 3, 4]); // Все категории

        // Тестовые пользователи
        User::insert([
            [
                'name' => 'Менеджер Мария',
                'email' => 'manager@test.ru',
                'password' => Hash::make('password'),
                'position_id' => 2,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Директор Иван',
                'email' => 'director@test.ru',
                'password' => Hash::make('password'),
                'position_id' => 3,
                'email_verified_at' => now(),
            ],
        ]);

        // Тестовая поездка
        Trip::create([
            'user_id' => 1,
            'car_id' => 1,
            'start_at' => now()->addDay()->setTime(10, 0),
            'end_at' => now()->addDay()->setTime(12, 0),
            'purpose' => 'Встреча с клиентом',
            'status' => 'planned',
        ]);
    }
}

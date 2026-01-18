## Car Rental API

это веб-приложение на Laravel 12, позволяющее сотрудникам компании находить и бронировать доступные служебные автомобили согласно их должности и уровню комфорта.

- Ролевой доступ — разные должности → разные категории авто
- Проверка занятости — учёт пересекающихся периодов
- Фильтрация — по модели и уровню комфорта
- JWT аутентификация — через Laravel Sanctum

## Требования

PHP 8.2+

Composer 2.5+

MySQL 8.0+ или SQLite 3.35+

Laravel 12


## Аутентификация
```
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"manager@test.ru","password":"password"}'
```

Ответ:
```
{
  "success": true,
  "token": "1|abcdefghijklmnopqrstuvwxyz123456",
  "user": {
    "id": 1,
    "name": "Менеджер Мария",
    "email": "manager@test.ru",
    "position": "Менеджер"
  }
}
```

Использование токена:
```
Authorization: Bearer {ваш_токен}
```

## Тестовые данные

После запуска сидов создаются:

Пользователи
- manager@test.ru | password | Менеджер | Эконом (1), Комфорт (2)
- director@test.ru | password | Директор | Все категории (1-4)

Автомобили (6 автомобилей)

Тестовая поездка
Автомобиль: Kia Rio (А001АА77)
Период: Завтра 10:00 - 12:00
Статус: Запланирована

### Endpoints

POST /api/login — Авторизация

POST /api/logout — Выход из системы
Требует аутентификации.

GET /api/me — Информация о текущем пользователе
Требует аутентификации.

GET /api/available-cars — Доступные автомобили
Возвращает список автомобилей, доступных для бронирования.

Пример запроса:
```
curl -X GET "http://localhost:8000/api/available-cars?start_at=2025-01-20%2010:00:00&end_at=2025-01-20%2012:00:00" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
  ```

Успешный ответ (200 OK):
```
{
  "success": true,
  "data": [
    {
      "id": 1,
      "plate_number": "А001АА77",
      "model": "Kia Rio",
      "category": {
        "name": "Эконом"
      },
      "driver": {
        "id": 1,
        "full_name": "Иванов Иван",
        "license_number": "АБ123456"
      },
      "is_available": true
    }
  ],
  "meta": {
    "total": 4,
    "user_position": "Менеджер",
    "available_categories": [1, 2]
  }
}
```

##  Примеры использования

Пример 1: Менеджер ищет все доступные авто

bash
Менеджер видит только автомобили категорий 1 и 2
```
curl -X GET "http://localhost:8000/api/available-cars?start_at=2025-01-21%2014:00:00&end_at=2025-01-21%2016:00:00" \
  -H "Authorization: Bearer {token_менеджера}"
```

Пример 2: Фильтр по уровню комфорта
bash
```
Только автомобили Комфорт-класса (уровень 2)
curl -X GET "http://localhost:8000/api/available-cars?start_at=2025-01-21%2014:00:00&end_at=2025-01-21%2016:00:00&comfort_level=2" \
  -H "Authorization: Bearer {token_менеджера}"
```

## Архитектура
```
app/
├── Http/
│   ├── Controllers/Api/
│   │   ├── AuthController.php          # Аутентификация
│   │   └── AvailableCarsController.php # Основной endpoint
│   ├── Requests/
│   │   └── AvailableCarsRequest.php    # Валидация
│   └── Resources/
│       └── AvailableCarResource.php    # Форматирование ответов
├── Models/
│   ├── User.php                        # Пользователи
│   ├── Position.php                    # Должности
│   ├── CarCategory.php                 # Категории авто
│   ├── CarModel.php                    # Модели авто
│   ├── Car.php                         # Автомобили
│   ├── Driver.php                      # Водители
│   └── Trip.php                        # Поездки
└── Services/
    └── CarAvailabilityService.php      # Бизнес-логика
```

## Логика работы

Проверка доступа
Менеджер может использовать только авто категорий 1 и 2
Директор может использовать все категории (1-4)

Проверка занятости
Автомобиль считается занятым, если имеет поездку, которая:
Начинается в запрашиваемом периоде
Заканчивается в запрашиваемом периоде
Полностью содержит запрашиваемый период

Бизнес-правила
Каждая должность имеет доступ к определённым категориям
Автомобиль может быть без водителя
Отменённые поездки не учитываются при проверке занятости


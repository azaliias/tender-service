### Быстрый старт

#### 1. Клонирование репозитория
```
git clone https://github.com/azaliias/tender-service.git
cd tender-service
```

#### 2. Настройка окружения
2.1 Создайте файл `.env` на основе файла-примера `.env.example`
2.2 Отредактируйте настройки БД в .env:
```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=tender_service
DB_USERNAME=root
DB_PASSWORD=
```

#### 3. Установка зависимостей
```
docker-compose run --rm app composer install
```

#### 4. Запуск через Docker
```
docker-compose up -d --build
```

#### 5. Установка миграций
```
docker-compose exec app php artisan migrate --seed
```

#### 6. Импорт данных из файла test_task_data.csv
```
docker cp test_task_data.csv tender-service-app-1:/var/www/html/storage/app/test_task_data.csv
docker-compose exec app php artisan import:tenders
```

### Доступ к API
##### Базовый URL: `http://localhost:8000/api`

### Начало работы
#### 1. Получение токена

**POST** `http://localhost:8000/api/auth/login`

Body:
```json
{
    "email": "admin@admin.ru",
    "password": "password"
}
```
Response:
```json
{
    "token": "1|Vo0FB7XZx0JpDfIJBK2Ns1E43y4IM7T3u5b6I8oHbeb7159d"
}
```
#### 2. Создание тендера

**POST** `http://localhost:8000/api/tenders`

Headers:
`Authorization: Bearer YOUR_TOKEN` - **YOUR_TOKEN** из ответа `auth/login`

Body:
```json
{
    "external_code": "1111111111",
    "number": "2025-06-01",
    "status": "Открыто",
    "name": "Тестовая заявка"
}
```
Response:
```json
{
    "message": "Tender created successfully",
    "data": {
        "external_code": "1111111111",
        "number": "2025-06-01",
        "status": "Открыто",
        "name": "Тестовая заявка",
        "updated_at": "18.06.2025 12:00:39",
        "created_at": "18.06.2025 12:00:39",
        "id": 5431
    }
}
```

### 3. Получение тендера с участием идентификатора

В качестве идентификатора задан внешний код (external_code)

**GET** `http://localhost:8000/api/tenders/{external_code}`

Headers:
`Authorization: Bearer YOUR_TOKEN` - **YOUR_TOKEN** из ответа `auth/login`

Пример:
```text
GET http://localhost:8000/api/tenders/152467080
```
Response:
```json
{
    "id": 19,
    "external_code": "152467080",
    "number": "17540-2",
    "status": "Закрыто",
    "name": "Запрос скидок Поставка флагов для ОАО Компания Череповец",
    "created_at": "14.08.2022 19:25:13",
    "updated_at": "14.08.2022 19:25:13"
}
```

#### 4. Получение списка тендеров

**GET** `http://localhost:8000/api/tenders`

Headers:
`Authorization: Bearer YOUR_TOKEN` - **YOUR_TOKEN** из ответа `auth/login`

Response:
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "external_code": "152467180",
            "number": "17660-2",
            "status": "Закрыто",
            "name": "Лабороаторная посуда",
            "created_at": "14.08.2022 19:25:14",
            "updated_at": "14.08.2022 19:25:14"
        },
        ...
    ],
    "pagination": {
        "total": 5430,
        "per_page": 10,
        "current_page": 1,
        "last_page": 543,
        "from": 1,
        "to": 10
    }
}
```

#### 5. Получение списка тендеров с фильтрацией по названию или дате с использованием query параметров name и date

**GET** `http://localhost:8000/api/tenders?name={name}&date={date}&page={page}`

Headers:
`Authorization: Bearer YOUR_TOKEN` - **YOUR_TOKEN** из ответа `auth/login`

Пример:
`http://localhost:8000/api/tenders?name=Поставка&date=2022-08-14&page=3`

Response:
```json
{
    "success": true,
    "data": [
        {
            "id": 55,
            "external_code": "152466840",
            "number": "17516-2",
            "status": "Закрыто",
            "name": "Поставка металлопроката в адрес БФ ОАО Компания г.Балаково ЗАПРОС СКИДКИ",
            "created_at": "14.08.2022 19:25:12",
            "updated_at": "14.08.2022 19:25:12"
        },
        ...
    ],
    "pagination": {
        "total": 1469,
        "per_page": 10,
        "current_page": 3,
        "last_page": 147,
        "from": 21,
        "to": 30
    }
}
```

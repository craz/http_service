---
auto_apply: true
apply_to:
  [
    "models/**/*.php",
    "controllers/**/*.php",
    "components/**/*.php",
    "modules/**/*.php",
  ]
---

# Правила для Yii2 Framework

## ActiveRecord модели

- Наследовать от `yii\db\ActiveRecord`
- Переопределять метод `tableName()` для указания имени таблицы
- Использовать метод `rules()` для валидации данных
- Добавлять `attributeLabels()` для локализации названий полей
- Использовать scope методы для повторяющихся запросов

## Контроллеры

- Наследовать от `yii\web\Controller` или `yii\console\Controller`
- Использовать фильтры для контроля доступа
- Возвращать Response объекты из действий
- Обрабатывать POST и GET запросы отдельно

## Представления (Views)

- Использовать виджеты Yii2 для форм и таблиц
- Экранировать все выводимые данные с помощью `Html::encode()`
- Использовать ActiveForm для создания форм
- Структурировать представления с помощью лейаутов

## Компоненты и сервисы

- Регистрировать компоненты в конфигурации приложения
- Использовать Dependency Injection где возможно
- Создавать сервисы для бизнес-логики

## Миграции

- Использовать миграции для изменений структуры базы данных
- Добавлять методы `up()` и `down()` для отката изменений
- Использовать транзакции для критических операций

## Работа с формами

- Использовать ActiveForm виджет
- Добавлять CSRF защиту
- Валидировать данные на стороне сервера

## Примеры кода

### ActiveRecord модель

```php
<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Модель продукта
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property float $price
 */
class Product extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'products';
    }

    public function rules(): array
    {
        return [
            [['name', 'price'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['price'], 'number', 'min' => 0],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'description' => 'Описание',
            'price' => 'Цена',
        ];
    }
}
```

### Контроллер

```php
<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Product;

class ProductController extends Controller
{
    public function actionIndex()
    {
        $products = Product::find()->all();

        return $this->render('index', [
            'products' => $products,
        ]);
    }

    public function actionView($id)
    {
        $product = $this->findModel($id);

        return $this->render('view', [
            'product' => $product,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая страница не найдена.');
    }
}
```

## Лучшие практики

- Использовать Gii для генерации базового кода
- Настраивать кэширование для производительности
- Использовать логирование для отладки
- Создавать unit тесты для критической функциональности
- Использовать fixtures для тестовых данных

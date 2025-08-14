---
auto_apply: true
apply_to: "modules/admin/**/*"
---

# Правила для админ модуля

## Структура админ модуля

- Располагать все файлы админки в папке `modules/admin/`
- Использовать отдельные лейауты для админской части
- Создавать отдельные контроллеры для каждой сущности

## Аутентификация и авторизация

- Использовать модель `Admin` для аутентификации администраторов
- Проверять права доступа перед выполнением действий
- Использовать фильтры доступа в контроллерах
- Реализовать систему ролей для разграничения прав

## Контроллеры админки

- Наследовать от базового `AdminController`
- Использовать CRUD операции для управления данными
- Добавлять проверки прав доступа в каждое действие
- Использовать GridView для отображения списков

## Формы и валидация

- Использовать ActiveForm для создания форм
- Добавлять клиентскую и серверную валидацию
- Использовать CSRF защиту во всех формах
- Добавлять подтверждение для операций удаления

## Пользовательский интерфейс

- Использовать единый стиль для всех страниц админки
- Добавлять breadcrumb навигацию
- Использовать flash сообщения для уведомлений
- Делать интерфейс интуитивно понятным

## Безопасность

- Экранировать все пользовательские данные
- Использовать подготовленные запросы
- Логировать все действия администраторов
- Ограничивать доступ к админке по IP (при необходимости)

## Примеры кода

### Базовый AdminController

```php
<?php

namespace app\modules\admin\components;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * Базовый контроллер для админ модуля
 */
class AdminController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity instanceof \app\models\Admin;
                        }
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    throw new ForbiddenHttpException('Доступ запрещен.');
                }
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        // Логирование действий администратора
        Yii::info('Admin action: ' . $action->id, 'admin');

        return true;
    }
}
```

### Контроллер для управления сущностью

```php
<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Product;
use app\modules\admin\components\AdminController;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

/**
 * Контроллер для управления продуктами
 */
class ProductController extends AdminController
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Product::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Product();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Продукт успешно создан.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Продукт успешно обновлен.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        Yii::$app->session->setFlash('success', 'Продукт успешно удален.');
        return $this->redirect(['index']);
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

## Лучшие практики для админки

- Использовать пагинацию для больших списков
- Добавлять поиск и фильтрацию в GridView
- Создавать удобную навигацию между разделами
- Использовать модальные окна для быстрых действий
- Добавлять подтверждения для критических операций
- Логировать все важные действия администраторов

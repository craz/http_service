<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\Manager $model */

$this->title = 'Менеджеры';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="m-0"><?= Html::encode($this->title) ?></h2>
    <a class="btn btn-primary" href="<?= Url::to(['create']) ?>">Добавить менеджера</a>
    <div></div>
</div>

<div class="manager-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $model,
        'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
        'columns' => [
            'id',
            'first_name',
            'last_name',
            'email:email',
            [
                'attribute' => 'schedule',
                'filter' => [
                    'day' => 'День',
                    'night' => 'Ночь',
                    'flex' => 'Плавающий',
                ],
                'value' => function ($m) {
                    return $m->schedule === 'night' ? 'Ночь' : ($m->schedule === 'flex' ? 'Плавающий' : 'День');
                }
            ],
            [
                'attribute' => 'status',
                'filter' => [
                    'active' => 'Активен',
                    'vacation' => 'В отпуске',
                    'sick' => 'Больничный',
                ],
                'value' => function ($m) {
                    return $m->status === 'vacation' ? 'В отпуске' : ($m->status === 'sick' ? 'Больничный' : 'Активен');
                }
            ],
            [
                'label' => 'Роли',
                'value' => function ($m) {
                    return implode(', ', array_map(fn($r) => $r->name, $m->roles));
                }
            ],
            [
                'class' => yii\grid\ActionColumn::class,
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>
</div>



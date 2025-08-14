<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Dispatch $model */

$this->title = 'Рассылки';
$this->params['breadcrumbs'][] = ['label' => 'Рассылки', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Просмотр записи #'.$model->id;

\yii\web\YiiAsset::register($this);

?>
<div class="dispatch-view">

	<p>
    	<?= Html::a('<i class="fa fa-pencil"></i>&nbsp; Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    	<?= Html::a('<i class="fa fa-trash"></i>&nbsp; Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
				'attribute' => 'created_at',
				'format' => ['raw'],
				'value' => function($data) {
					return date('Y-m-d H:i:s', strtotime($data->created_at));
				},

			],
            'name',
            'text:ntext',
            'age_min',
            'age_max',
            [
				'attribute' => 'gender',
				'format' => ['raw'],
				'value' => function($data) {

					$ret = '';

					if ($data->gender == 1)
						$ret = 'М';
					elseif ($data->gender == 2)
						$ret = 'Ж';

					return $ret;
				},

			],

			[
				'attribute' => 'is_registered',
				'format' => ['raw'],
				'value' => function($data) {

					$ret = '';

					if ($data->is_registered == 1) {
						$ret = 'Не зарегистрирован, не знаю о ней';
					} elseif ($data->is_registered == 2) {
						$ret = 'Не зарегистрирован';
					} elseif ($data->is_registered == 3) {
						$ret = 'Зарегистрирован';
					}

					return $ret;

				},

			],

			[
				'attribute' => 'using_frequency',
				'format' => ['raw'],
				'value' => function($data) {

					$ret = '';

					if ($data->using_frequency == 1) {
						$ret = 'Редко';
					} elseif ($data->using_frequency == 2) {
						$ret = 'Часто';
					} elseif ($data->using_frequency == 3) {
						$ret = 'Продаю';
					} elseif ($data->using_frequency == 4) {
						$ret = 'Не использую avon';
					} elseif ($data->using_frequency == 5) {
						$ret = 'Не покупаю';
					}

					return $ret;

				},

			],

			[
				'attribute' => 'favorite_products',
				'format' => ['raw'],
				'value' => function($data) {

					$ret = '';

					if ($data->favorite_products == 1) {
						$ret = 'Уход';
					} elseif ($data->favorite_products == 2) {
						$ret = 'Декоративная косметика';
					} elseif ($data->favorite_products == 3) {
						$ret = 'Ароматы';
					} elseif ($data->favorite_products == 4) {
						$ret = 'Тело';
					}

					return $ret;

				},

			],

			[
				'attribute' => 'refusal_reason',
				'format' => ['raw'],
				'value' => function($data) {

					$ret = '';

					if ($data->refusal_reason == 1) {
						$ret = 'Доставка';
					} elseif ($data->refusal_reason == 2) {
						$ret = 'Стоимость';
					} elseif ($data->refusal_reason == 3) {
						$ret = 'Качество продукции';
					} elseif ($data->refusal_reason == 4) {
						$ret = 'Не знаком с брендом';
					}

					return $ret;

				},

			],
            'date_start',
            'is_sended',
        ],
    ]) ?>

</div>

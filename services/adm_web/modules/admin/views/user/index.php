<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\helpers\ArrayHelper;
use kartik\daterange\DateRangePicker;

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

	<?= GridView::widget([

    	'dataProvider' => $dataProvider,

    	'id'=>$model->tableName().'-grid',
        'filterModel' => $model,
        'tableOptions' => [
	        'class' => 'table table-striped table-bordered table-hover',
        ],

        'columns' => [
        	[
            	'attribute' => 'id',
				'headerOptions'=>[
	                 'class'=>'w11',
                 ],
			],

			[
	            'attribute' => 'created_at',
	            'headerOptions'=>[
	                 'class'=>'w12',
                 ],
                'value'=>function($model) {
                    return date('d.m.Y, H:i:s', strtotime($model->created_at));
                },
                'filter' => DateRangePicker::widget([
                    'model' => $model,
                    'attribute' => 'created_at',
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'd.m.Y'
                        ],
                    ],
                ]),
            ],

            'name',

            [
	            'attribute' => 'birth_date',
	            'headerOptions'=>[
	                 'class'=>'w12',
                 ],
                'value'=>function($model) {

                	$ret = $model->birth_date ? date('d.m.Y', strtotime($model->birth_date)) : "";

                	if ($ret == '01.01.1970')
                		$ret = '-';                	

                    return $ret;
                },
                'filter' => DateRangePicker::widget([
                    'model' => $model,
                    'attribute' => 'birth_date',
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'd.m.Y'
                        ],
                    ],
                ]),
            ],

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
				'filter' => [ 1 => 'М', 2 => 'Ж'],
				'contentOptions'=>[
					'class' => 'nowrap',
				],

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
				'filter' => [ 1 => 'Не зарегистрирован, не знаю о ней', 2 => 'Не зарегистрирован', 3 => 'Зарегистрирован'],
				'contentOptions'=>[
					'class' => 'nowrap',
				],

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
				'filter' => [ 1 => 'Редко', 2 => 'Часто', 3 => 'Продаю', 4 => 'Не использую avon', 5 => 'Не покупаю'],
				'contentOptions'=>[
					'class' => 'nowrap',
				],

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
				'filter' => [ 1 => 'Уход', 2 => 'Декоративная косметика', 3 => 'Ароматы', 4 => 'Тело'],
				'contentOptions'=>[
					'class' => 'nowrap',
				],

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
				'filter' => [ 1 => 'Доставка', 2 => 'Стоимость', 3 => 'Качество продукции', 4 => 'Не знаком с брендом'],
				'contentOptions'=>[
					'class' => 'nowrap',
				],

			],
        ],
    ]); ?>

</div>

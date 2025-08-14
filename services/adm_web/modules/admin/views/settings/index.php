<?php

use app\models\Settings;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

use yii\helpers\ArrayHelper;
use kartik\daterange\DateRangePicker;

$this->title = 'Статический контент';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settings-index">

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

            'title',
            'alias',

            [
				'attribute' => 'val',
				'format' => ['raw'],
				'value' => function($data) {
					return nl2br($data->val);
				}
			],

            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 },
                 'headerOptions'=>[
	                 'class'=>'w13',
                 ],
                 'contentOptions'=>[
	                 'class'=>'gridActions',
                 ],
               	 'visibleButtons'=>[
               	 	'update' => function ($model) {
						return true;
					},
					'delete' => function ($model) {
						return false;
					},
					'view' => function ($model) {
						return true;
					},
                ],
            ],
        ],
    ]); ?>


</div>

<?php

use app\models\PromotedTopic;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

use yii\helpers\ArrayHelper;
use kartik\daterange\DateRangePicker;

$this->title = 'Продвигаемые темы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="promoted-topic-index">

	<div class="preGridButtons">
        <a class="btn btn-success" href="/admin/promotedtopic/create"><i class="fa fa-plus" aria-hidden="true"></i> Добавить</a>        
    </div>

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
			'topic',

			[
				'attribute' => 'disabled',
				'headerOptions'=>[
	                 'class'=>'w12',
                 ],
				'format' => ['raw'],
				'value' => function($data) {

					$ret = '';

					if ($data->disabled) {
						$ret = 'Отключено';
					}

					return $ret;

				},
				'filter' => [ 1 => 'Отключено', 0 => 'Включено'],
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
                ],
            ],

        ],
    ]); ?>

</div>

<?php

use app\models\Image;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

use yii\helpers\ArrayHelper;
use kartik\daterange\DateRangePicker;

use app\models\Holiday;

$this->title = 'Образы';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="image-index">

	<div class="preGridButtons">
        <a class="btn btn-success" href="/admin/image/create"><i class="fa fa-plus" aria-hidden="true"></i> Добавить</a>        
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

            [
				'attribute' => 'holiday_id',
				'format' => ['raw'],
				'value' => function($data) {

					$ret = $data->getHoliday()->one();

					if (!$ret) {
						return "";
					}

					return $ret->name;
				},
				'filter' => ArrayHelper::map(Holiday::find()->all(), 'id', 'name')
			],

            'disabled:boolean',

            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Image $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>

<?php

use app\models\HolidayExample;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\helpers\ArrayHelper;
use kartik\daterange\DateRangePicker;

use app\models\Holiday;

$this->title = 'Подбор праздника';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="holiday-example-index">

	<div class="preGridButtons">
        <a class="btn btn-success" href="/admin/holiday-example/create"><i class="fa fa-plus" aria-hidden="true"></i> Добавить</a>        
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

            [
				'attribute' => 'id_holiday',
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

            'content:ntext',

            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, HolidayExample $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>

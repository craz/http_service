<?php

use app\models\UnwantedInformationExample;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\helpers\ArrayHelper;
use kartik\daterange\DateRangePicker;

use app\models\UnwantedInformation;

$this->title = 'Нежелательная информация';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unwanted-information-example-index">

	<div class="preGridButtons">
        <a class="btn btn-success" href="/admin/unwanted-information-example/create"><i class="fa fa-plus" aria-hidden="true"></i> Добавить</a>        
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
				'attribute' => 'id_unwanted_information',
				'format' => ['raw'],
				'value' => function($data) {

					$ret = $data->getUnwantedInformation()->one();

					if (!$ret) {
						return "";
					}

					return $ret->name;
				},

				'filter' => ArrayHelper::map(UnwantedInformation::find()->all(), 'id', 'name')
			],

            'text:raw',

            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, UnwantedInformationExample $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>

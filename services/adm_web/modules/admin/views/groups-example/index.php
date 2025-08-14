<?php

use app\models\GroupsExample;
use app\models\StructureCatalog;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\helpers\ArrayHelper;
use kartik\daterange\DateRangePicker;

$this->title = 'Подбор продуктов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="groups-example-index">

	<div class="preGridButtons">
        <a class="btn btn-success" href="/admin/groups-example/create"><i class="fa fa-plus" aria-hidden="true"></i> Добавить</a>        
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
				'attribute' => 'id_class',
				'format' => ['raw'],
				'value' => function($data) {
					$group = $data->getStructureCatalog()->one();

					if (!$group)
						return "";

					return $group->category;
				},
				'filter' => ArrayHelper::map(StructureCatalog::find()->all(), 'id', 'category')

			],

            'product:ntext',
            'assignment:ntext',
            'description:ntext',

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
               	 	'delete' => false
                ],
            ],
        ],
    ]); ?>


</div>

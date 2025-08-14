<?php

use app\models\Product;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

use yii\helpers\ArrayHelper;
use kartik\daterange\DateRangePicker;

$this->title = 'Продукты';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="product-index">

	<div class="preGridButtons">
        <a class="btn btn-success" href="/admin/product/create"><i class="fa fa-plus" aria-hidden="true"></i> Добавить</a>
    </div>

    <?= GridView::widget([

    	'dataProvider' => $dataProvider,

    	'id'=>$model->tableName().'-grid',
        'filterModel' => $model,
        'tableOptions' => [
	        'class' => 'table table-striped table-bordered table-hover',
        ],

        'columns' => [

        	'id',
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
            'category',
            'group_name',
            'segment',
            'link',
            'disabled:boolean',
            'answer1',
            'answer2',
            'answer3',
            'group_new',

            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Product $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>

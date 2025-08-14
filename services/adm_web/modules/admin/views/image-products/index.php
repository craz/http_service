<?php

use app\models\ImageProducts;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

use yii\helpers\ArrayHelper;
use kartik\daterange\DateRangePicker;

use app\models\Image;
use app\models\Product;

$this->title = 'Продукты к образам';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="image-products-index">

	<div class="preGridButtons">
        <a class="btn btn-success" href="/admin/image-products/create"><i class="fa fa-plus" aria-hidden="true"></i> Добавить</a>        
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
				'attribute' => 'id_images',
				'format' => ['raw'],
				'value' => function($data) {

					$ret = $data->getImages()->one();

					if (!$ret) {
						return "";
					}

					return $ret->name;
				},
				'filter' => ArrayHelper::map(Image::find()->all(), 'id', 'name')
			],
			[
				'attribute' => 'id_product',
				'format' => ['raw'],
				'value' => function($data) {

					$ret = $data->getProduct()->one();

					if (!$ret) {
						return "";
					}

					return $ret->name;
				},
				'filter' => ArrayHelper::map(Product::find()->all(), 'id', 'name')	
			],

            'description:ntext',
            
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, ImageProducts $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>

<?php

use app\models\UserQuiz;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

use yii\helpers\ArrayHelper;
use kartik\daterange\DateRangePicker;

$this->title = 'Анкеты';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-quiz-index">

	<div class="preGridButtons">
		<form id="export-data-form" method="post" action="/admin/user-quiz/export"><input name="filter" type="hidden" value="" /><input name="export-data-submit" type="hidden" value="1" /><input name="params" type="hidden" value="" /></form>
		<a class="btn btn-primary js-export-data-form" href="javascript:;"><i class="fa fa-upload"></i> Экспорт с текущими фильтрами</a>
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
	            'attribute' => 'user_id',
	            'format' => ['raw'],
                'value'=>function($model) {
                    return "<a href='/admin/user?User%5Bid%5D=".$model->user_id."'>".$model->user_id."</a>";
                },
			],

            'answer1',
            'answer2',
            'answer3',
            'answer4',
            'answer5',
            'result1',
        ],
    ]); ?>


</div>

<?php

use app\models\DispatchPushes;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Информ рассылки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dispatch-pushes-index">

	<div class="preGridButtons">
        <a class="btn btn-success" href="/admin/dispatch-pushes/create"><i class="fa fa-plus" aria-hidden="true"></i> Добавить</a>        
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
				'attribute' => 'daytype',
				'format' => ['raw'],
				'value' => function($data) {

					if ($data->daytype == 1)
						return 'ПН';
					elseif ($data->daytype == 4)
						return 'ЧТ';
					elseif ($data->daytype == 5)
						return 'ВТ и ПТ';

					return '';
				},
				'filter' => [
					1 => 'ПН',
					4 => 'ЧТ',
					5 => 'ВТ и ПТ'
				]
			],

            [
				'attribute' => 'body',
				'format' => ['raw'],
				'value' => function($data) {
					return nl2br($data->body);
				}
			],

            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, DispatchPushes $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>

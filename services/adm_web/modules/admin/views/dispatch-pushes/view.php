<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\DispatchPushes $model */

$this->title = 'Информ рассылки';
$this->params['breadcrumbs'][] = ['label' => 'Информ рассылки', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Просмотр записи #'.$model->id;

\yii\web\YiiAsset::register($this);
?>
<div class="dispatch-pushes-view">

	<p>
    	<?= Html::a('<i class="fa fa-pencil"></i>&nbsp; Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    	<?= Html::a('<i class="fa fa-trash"></i>&nbsp; Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
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
			],

            [
				'attribute' => 'body',
				'format' => ['raw'],
				'value' => function($data) {
					return nl2br($data->body);
				}
			],
        ],
    ]) ?>

</div>

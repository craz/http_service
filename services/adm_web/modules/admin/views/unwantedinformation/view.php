<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\UnwantedInformation $model */

$this->title = 'Нежелательная информация';
$this->params['breadcrumbs'][] = ['label' => 'Нежелательная информация', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Просмотр записи #'.$model->id;

\yii\web\YiiAsset::register($this);
?>
<div class="unwanted-information-view">

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
				'attribute' => 'created_at',
				'format' => ['raw'],
				'value' => function($data) {
					return date('Y-m-d H:i:s', strtotime($data->created_at));
				},

			],
            'name',
            'content:ntext',
        ],
    ]) ?>

</div>

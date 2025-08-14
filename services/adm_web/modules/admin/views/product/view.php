<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Продукты';
$this->params['breadcrumbs'][] = ['label' => 'Продукты', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Просмотр записи #'.$model->id;

?>
<div class="product-view">

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
            'category',
            'group_name',
            'segment',
            'link',
            'disabled:boolean',
            'answer1',
            'answer2',
            'answer3',
            'group_new',
        ],
    ]) ?>

</div>

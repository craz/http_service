<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Продукты к образам';
$this->params['breadcrumbs'][] = ['label' => 'Продукты к образам', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Просмотр записи #'.$model->id;

\yii\web\YiiAsset::register($this);
?>
<div class="image-products-view">

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

			],
            'description:ntext',
        ],
    ]) ?>

</div>

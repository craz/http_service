<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\GroupsExample $model */

$this->title = 'Подбор продуктов';
$this->params['breadcrumbs'][] = ['label' => 'Подбор продуктов', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Просмотр записи #'.$model->id;

\yii\web\YiiAsset::register($this);
?>
<div class="groups-example-view">

    <p>
    	<?= Html::a('<i class="fa fa-pencil"></i>&nbsp; Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
				'attribute' => 'id_class',
				'format' => ['raw'],
				'value' => function($data) {
					$group = $data->getStructureCatalog()->one();

					if (!$group)
						return "";

					return $group->category;
				},

			],
            'product:ntext',
            'assignment:ntext',
            'description:ntext',
        ],
    ]) ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\StructureCatalog $model */

$this->title = 'Группы продуктов';
$this->params['breadcrumbs'][] = ['label' => 'Группы продуктов', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Просмотр записи #'.$model->id;

\yii\web\YiiAsset::register($this);
?>
<div class="structure-catalog-view">

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
            'category',
            'question1',
            'question2',
            'question3',
        ],
    ]) ?>

</div>

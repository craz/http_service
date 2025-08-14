<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Settings $model */

$this->title = 'Статический контент';
$this->params['breadcrumbs'][] = ['label' => 'Статический контент', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Просмотр записи #'.$model->id;

\yii\web\YiiAsset::register($this);
?>
<div class="settings-view">

	<p>
    	<?= Html::a('<i class="fa fa-pencil"></i>&nbsp; Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'alias',
            [
				'attribute' => 'val',
				'format' => ['raw'],
				'value' => function($data) {
					return nl2br($data->val);
				}
			],
        ],
    ]) ?>

</div>

<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/** @var int $activeModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\RoutingRule $searchModel */

$this->title = 'Управление контекстом';
$this->params['breadcrumbs'][] = $this->title;
?>

<ul class="nav nav-tabs mb-3">
  <?php for ($i=1; $i<=3; $i++): ?>
    <li class="nav-item">
      <a class="nav-link <?= $i===$activeModel ? 'active' : '' ?>" href="<?= Url::to(['index', 'model'=>$i]) ?>">Модель <?= $i ?></a>
    </li>
  <?php endfor; ?>
  <li class="ms-auto">
    <a class="btn btn-sm btn-primary" href="<?= Url::to(['create', 'model'=>$activeModel]) ?>">Добавить правило</a>
  </li>
</ul>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'tableOptions' => ['class'=>'table table-bordered table-striped'],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'indata',
            'format' => 'ntext',
            'contentOptions' => ['style'=>'white-space:pre-wrap;'],
        ],
        [
            'attribute' => 'target_model_type',
            'filter' => [
                'контейнер' => 'контейнер',
                'опт' => 'опт',
                'розница' => 'розница',
                'спецтехника' => 'спецтехника',
            ],
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'controller' => 'context',
        ],
    ],
]); ?>



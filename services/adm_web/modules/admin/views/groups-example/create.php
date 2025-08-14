<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\GroupsExample $model */

$this->title = 'Подбор продуктов';
$this->params['breadcrumbs'][] = ['label' => 'Подбор продуктов', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Добавление записи';

?>
<div class="groups-example-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\DispatchPushes $model */

$this->title = 'Информ рассылки';
$this->params['breadcrumbs'][] = ['label' => 'Информ рассылки', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование записи #'.$model->id;

?>
<div class="dispatch-pushes-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

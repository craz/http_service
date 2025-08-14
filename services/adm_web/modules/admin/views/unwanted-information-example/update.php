<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\UnwantedInformationExample $model */

$this->title = 'Нежелательная информация';
$this->params['breadcrumbs'][] = ['label' => 'Нежелательная информация', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование записи #'.$model->id;

?>
<div class="unwanted-information-example-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

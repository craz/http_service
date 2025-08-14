<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\UnwantedInformation $model */

$this->title = 'Нежелательная информация';
$this->params['breadcrumbs'][] = ['label' => 'Нежелательная информация', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование записи #'.$model->id;

?>
<div class="unwanted-information-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

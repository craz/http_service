<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Image $model */

$this->title = 'Образы';
$this->params['breadcrumbs'][] = ['label' => 'Образы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование записи #'.$model->id;

?>
<div class="image-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

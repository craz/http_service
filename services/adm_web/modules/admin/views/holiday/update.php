<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Holiday $model */

$this->title = 'Праздники';
$this->params['breadcrumbs'][] = ['label' => 'Праздники', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование записи #'.$model->id;

?>
<div class="holiday-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

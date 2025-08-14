<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\HolidayExample $model */

$this->title = 'Подбор праздника';
$this->params['breadcrumbs'][] = ['label' => 'Подбор праздника', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование записи #'.$model->id;

?>
<div class="holiday-example-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

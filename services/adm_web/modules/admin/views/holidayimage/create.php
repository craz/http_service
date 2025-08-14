<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\HolidayImage $model */

$this->title = 'Create Holiday Image';
$this->params['breadcrumbs'][] = ['label' => 'Holiday Images', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="holiday-image-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

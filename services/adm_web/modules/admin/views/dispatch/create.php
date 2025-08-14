<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Dispatch $model */

$this->title = 'Рассылки';
$this->params['breadcrumbs'][] = ['label' => 'Рассылки', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Добавление записи';

?>
<div class="dispatch-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

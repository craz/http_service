<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Holiday $model */

$this->title = 'Праздники';
$this->params['breadcrumbs'][] = ['label' => 'Праздники', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Добавление записи';

?>
<div class="holiday-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

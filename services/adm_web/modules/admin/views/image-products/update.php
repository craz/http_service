<?php

use yii\helpers\Html;

$this->title = 'Продукты к образам';
$this->params['breadcrumbs'][] = ['label' => 'Продукты к образам', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование записи #'.$model->id;

?>
<div class="image-products-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

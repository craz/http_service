<?php

use yii\helpers\Html;

$this->title = 'Продукты к образам';
$this->params['breadcrumbs'][] = ['label' => 'Продукты к образам', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Добавление записи';

?>
<div class="image-products-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

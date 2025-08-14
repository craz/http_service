<?php

use yii\helpers\Html;

$this->title = 'Образы';
$this->params['breadcrumbs'][] = ['label' => 'Образы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Добавление записи';

?>
<div class="image-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

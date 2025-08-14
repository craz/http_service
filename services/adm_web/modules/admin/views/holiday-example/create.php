<?php

use yii\helpers\Html;

$this->title = 'Подбор праздника';
$this->params['breadcrumbs'][] = ['label' => 'Подбор праздника', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Добавление записи';

?>
<div class="holiday-example-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

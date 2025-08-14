<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\StructureCatalog $model */

$this->title = 'Группы продуктов';
$this->params['breadcrumbs'][] = ['label' => 'Группы продуктов', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование записи #'.$model->id;

?>
<div class="structure-catalog-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

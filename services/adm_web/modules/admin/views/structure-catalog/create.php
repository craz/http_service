<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\StructureCatalog $model */

$this->title = 'Группы продуктов';
$this->params['breadcrumbs'][] = ['label' => 'Группы продуктов', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Добавление записи';

?>
<div class="structure-catalog-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

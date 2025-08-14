<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\PromotedTopic $model */

$this->title = 'Продвигаемые темы';
$this->params['breadcrumbs'][] = ['label' => 'Продвигаемые темы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование записи #'.$model->id;

?>
<div class="promoted-topic-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

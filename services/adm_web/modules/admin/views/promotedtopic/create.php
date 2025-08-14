<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\PromotedTopic $model */

$this->title = 'Продвигаемые темы';
$this->params['breadcrumbs'][] = ['label' => 'Продвигаемые темы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Добавление записи';

?>
<div class="promoted-topic-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

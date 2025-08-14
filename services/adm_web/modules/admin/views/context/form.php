<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var app\models\RoutingRule $model */
/** @var int $activeModel */

$this->title = ($model->isNewRecord ? 'Создать' : 'Изменить') . ' правило (Модель ' . (int)$activeModel . ')';
$this->params['breadcrumbs'][] = ['label'=>'Управление контекстом', 'url'=>['index', 'model'=>$activeModel]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card">
  <div class="card-body">
    <?php $form = ActiveForm::begin(); ?>
      <?= $form->field($model, 'model_number')->hiddenInput()->label(false) ?>
      <?= $form->field($model, 'indata')->textarea(['rows'=>5]) ?>
      <?= $form->field($model, 'target_model_type')->dropDownList([
        'контейнер'=>'контейнер', 'опт'=>'опт', 'розница'=>'розница', 'спецтехника'=>'спецтехника'
      ], ['prompt'=>'Выберите...']) ?>
      <div class="text-end">
        <button class="btn btn-primary">Сохранить</button>
      </div>
    <?php ActiveForm::end(); ?>
  </div>
</div>



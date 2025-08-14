<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Manager $model */
/** @var app\models\Role[] $roles */

$this->title = $model->isNewRecord ? 'Добавить менеджера' : 'Редактировать менеджера';
$this->params['breadcrumbs'][] = ['label' => 'Менеджеры', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card">
    <div class="card-body">
        <?php $form = ActiveForm::begin(); ?>

        <div class="row g-3">
            <div class="col-md-6">
                <?= $form->field($model, 'first_name')->textInput() ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'last_name')->textInput() ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'email')->textInput() ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'tg_username')->textInput() ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'tg_id')->textInput() ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'schedule')->dropDownList([
                    'day' => 'День',
                    'night' => 'Ночь',
                    'flex' => 'Плавающий',
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'status')->dropDownList([
                    'active' => 'Активен',
                    'vacation' => 'В отпуске',
                    'sick' => 'Больничный',
                ]) ?>
            </div>
        </div>

        <hr/>
        <h5>Роли</h5>
        <div class="row">
            <?php
            $selected = array_map(fn($r) => $r->id, $model->roles);
            foreach ($roles as $role): ?>
                <div class="col-md-4 form-check mb-2">
                    <input type="checkbox" class="form-check-input" id="role-<?= $role->id ?>" name="roles[]" value="<?= $role->id ?>" <?= in_array($role->id, $selected, true) ? 'checked' : '' ?>>
                    <label for="role-<?= $role->id ?>" class="form-check-label"><?= Html::encode($role->name) ?></label>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-3">
            <button class="btn btn-primary" type="submit">Сохранить</button>
            <a class="btn btn-secondary" href="<?= \yii\helpers\Url::to(['index']) ?>">Отмена</a>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
 </div>



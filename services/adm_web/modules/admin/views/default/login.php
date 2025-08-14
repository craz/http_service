<?php

use yii\helpers\Html;
use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap5\ActiveForm;
use yii\widgets\Pjax;
use yii\widgets\Menu;
use yii\widgets\ListView;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Breadcrumbs;
use yii\bootstrap5\Alert;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

?>

<div class="d-flex flex-column align-items-center mt-5">
    <picture class="mb-3">
        <source srcset="/uploads/logo.svg" type="image/svg+xml">
        <source srcset="/uploads/logo.png" type="image/png">
        <img src="/uploads/logo.jpg" alt="Логотип ИВАН ВЕЗЕТ" style="height:64px;object-fit:contain" onerror="this.parentElement.replaceWith(document.createElement('div'));"/>
    </picture>
    <div class="fw-semibold mb-3" style="letter-spacing: .02em;">ИВАН ВЕЗЕТ</div>

    <?php $form = ActiveForm::begin([
        'errorSummaryCssClass'=>'alert alert-danger',
        'fieldConfig' => [
            'options' => [
                'tag' => false,
            ],
        ],
        'options'=>[
            'class'=>'form-signin w-100',
            'style'=>'max-width:360px',
        ],
        'enableClientValidation'=>false
    ]); ?>

        <?= $form->errorSummary($model, [
            'header'=>'',
        ]) ?>

        <?= $form->field($model, 'login', [
            'inputOptions'=>[
                'autofocus'=>'autofocus',
                'required'=>'required',
                'placeholder'=>'Логин',
                'class'=>'form-control form-control-sm text-center',
            ],
        ])->textInput(['maxlength' => true])->label(false) ?>

        <?= $form->field($model, 'password', [
            'inputOptions'=>[
                'required'=>'required',
                'placeholder'=>'Пароль',
                'class'=>'form-control form-control-sm text-center',
            ],
        ])->passwordInput(['maxlength' => true])->label(false) ?>

        <?php if (!YII_ENV_DEV): ?>
            <?= $form->field($model, 'reCaptcha')->widget(\himiklab\yii2\recaptcha\ReCaptcha2::class)->label(false) ?>
        <?php endif; ?>

        <?php $this->registerCss(':root{ --brand-color:#ff9800 } .btn-primary{ background-color:var(--brand-color); border-color:var(--brand-color);} .btn-primary:hover{ filter:brightness(.95);}'); ?>
        <?= Html::submitButton('Войти', [
            'class'=>'btn btn-primary btn-sm w-100 mb-2',
        ]) ?>

        <button type="button" class="btn btn-outline-secondary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#resetModal">Сброс пароля</button>

    <?php ActiveForm::end(); ?>
</div>

<!-- Modal: Сброс пароля -->
<div class="modal fade" id="resetModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Сброс пароля</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2 small text-muted">Без регистрации — доступ выдает только админ</div>
        <input type="email" class="form-control" placeholder="Введите e-mail" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
        <button type="button" class="btn btn-primary">Отправить ссылку</button>
      </div>
    </div>
  </div>
</div>

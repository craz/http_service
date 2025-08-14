<?php

use yii\helpers\Html;

use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;

use kartik\datetime\DateTimePicker;

/** @var yii\web\View $this */
/** @var app\models\Dispatch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="dispatch-form admin-form-container">

    <?php $form = ActiveForm::begin([
    	'layout' => 'horizontal',
        'errorSummaryCssClass'=>'alert alert-danger',
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'offset' => 'col-sm-offset-3',
                'label' => 'col-sm-3',
                'wrapper' => 'col-sm-9',
                'error' => '',
                'hint' => 'col-sm-3',
            ],
        ],
        'enableClientValidation'=>false,
        'options' => ['enctype' => 'multipart/form-data']
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'gender')->dropDownList([0 => '', 1=>'М',2=>'Ж']); ?>

    <?= $form->field($model, 'age_min')->textInput() ?>

    <?= $form->field($model, 'age_max')->textInput() ?>

    <?= $form->field($model, 'is_registered')->dropDownList([0 => '', 1 => 'Не зарегистрирован, не знаю о ней', 2 => 'Не зарегистрирован', 3 => 'Зарегистрирован']); ?>

    <?= $form->field($model, 'using_frequency')->dropDownList([0 => '', 1 => 'Редко', 2 => 'Часто', 3 => 'Продаю', 4 => 'Не использую avon', 5 => 'Не покупаю']) ?>

    <?= $form->field($model, 'favorite_products')->dropDownList([0 => '', 1 => 'Уход', 2 => 'Декоративная косметика', 3 => 'Ароматы', 4 => 'Тело']) ?>

    <?= $form->field($model, 'refusal_reason')->dropDownList([0 => '', 1 => 'Доставка', 2 => 'Стоимость', 3 => 'Качество продукции', 4 => 'Не знаком с брендом']) ?>

    <?= $form->field($model, 'date_start')->widget(DateTimePicker::classname(), [
	    'options' => ['placeholder' => ''],
	    'pluginOptions' => [
	        'autoclose' => true,
	        'format' => 'yyyy-mm-dd hh:ii:ss'
	    ]
	]) ?>

    <div class="form-group row">
    	<div class="col-sm-3"></div>
        <div class="col-sm-9">
	        <?= Html::submitButton('<i class="fa fa-check fa-lg"></i> Сохранить', ['class' => 'btn btn-primary']) ?>
		</div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

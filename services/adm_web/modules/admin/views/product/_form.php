<?php

use yii\helpers\Html;

use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;

?>

<div class="product-form admin-form-container">

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

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'category')->textInput() ?>

    <?= $form->field($model, 'group_name')->textInput() ?>

    <?= $form->field($model, 'segment')->textInput() ?>

    <?= $form->field($model, 'link')->textInput() ?>

    <?= $form->field($model, 'disabled')->checkbox() ?>

    <?= $form->field($model, 'answer1')->textInput() ?>

    <?= $form->field($model, 'answer2')->textInput() ?>

    <?= $form->field($model, 'answer3')->textInput() ?>

    <?= $form->field($model, 'group_new')->textInput() ?>

    <div class="form-group row">
    	<div class="col-sm-3"></div>
        <div class="col-sm-9">
	        <?= Html::submitButton('<i class="fa fa-check fa-lg"></i> Сохранить', ['class' => 'btn btn-primary']) ?>
		</div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

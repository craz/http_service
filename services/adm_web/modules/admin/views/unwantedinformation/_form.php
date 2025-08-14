<?php

use yii\helpers\Html;

use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
?>

<div class="unwanted-information-form admin-form-container">

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

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>


    <div class="form-group row">
    	<div class="col-sm-3"></div>
        <div class="col-sm-9">
	        <?= Html::submitButton('<i class="fa fa-check fa-lg"></i> Сохранить', ['class' => 'btn btn-primary']) ?>
		</div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

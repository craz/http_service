<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception$exception */

use yii\helpers\Html;

$this->title = \Yii::$app->name;

?>
<div class="site-error" style_="display:none">
	<?= nl2br(Html::encode($message)) ?>
</div>

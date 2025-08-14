<?php

use yii\helpers\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\assets\AdminAppCoreAsset;
use app\assets\AdminAppLoginAsset;
use yii\bootstrap5\Alert;

AdminAppCoreAsset::register($this);
AdminAppLoginAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>

    <title><?= Yii::$app->name ?> | Панель управления | Вход</title>

    <!-- Bootstrap (CDN) -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="login text-center">
<?php $this->beginBody() ?>

    <?= $content ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
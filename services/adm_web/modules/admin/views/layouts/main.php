<?php
/** @var \yii\web\View $this */

use yii\helpers\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\assets\AdminAppAsset;
use yii\bootstrap5\Alert;
use yii\web\JqueryAsset;
use yii\web\View as YiiWebView;
use newerton\fancybox\FancyBox;
use Yii;

use app\models\Feedback;

$this->registerAssetBundle(\app\assets\AdminAppAsset::class);

$this->registerAssetBundle(JqueryAsset::class, YiiWebView::POS_HEAD);

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

    <title><?= Yii::$app->name ?> | Панель управления | <?= Html::encode($this->title) ?></title>

    <!-- SB Admin template assets -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net"/>
    <link rel="preconnect" href="https://use.fontawesome.com"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/gh/StartBootstrap/startbootstrap-sb-admin@master/dist/css/styles.css" rel="stylesheet" />
<?php
$this->registerCss('\n:root{ --brand-color:#ff9800; }\n.sb-topnav.navbar{ background-color: var(--brand-color) !important; background-image:none !important; filter:none !important; border-bottom:0 !important; }\n.navbar.bg-dark{ background-color: var(--brand-color) !important; }\n.sb-topnav.navbar .navbar-brand, .sb-topnav.navbar .nav-link{ color:#fff !important; }\n');
?>
</head>
<body>
<?php $this->beginBody() ?>

<?php
    $successFlash = \Yii::$app->session->getFlash('success');
    $errorFlash = \Yii::$app->session->getFlash('error');

    echo FancyBox::widget([
        'target' => 'a[class=fancybox]',
        'helpers' => true,
        'mouse' => true,
    ]);
?>

<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3 d-flex align-items-center gap-2" href="<?= Url::to(['/admin']) ?>">
        <picture>
            <source srcset="/uploads/logo.svg" type="image/svg+xml">
            <source srcset="/uploads/logo.png" type="image/png">
            <img src="/uploads/logo.jpg" alt="Логотип" style="height:24px;object-fit:contain" onerror="this.style.display='none'"/>
        </picture>
        <span><?= Html::encode(Yii::$app->name) ?></span>
    </a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" type="button"><i class="fas fa-bars"></i></button>
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item"><a class="nav-link" href="<?= Url::to(['/site/logout']) ?>">Выйти</a></li>
    </ul>
</nav>

<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Навигация</div>
                    <a class="nav-link" href="<?= Url::to(['/admin/user']) ?>">Пользователи</a>
                    <a class="nav-link" href="<?= Url::to(['/admin/communication']) ?>">Центр коммуникации</a>
                    <a class="nav-link" href="<?= Url::to(['/admin/manager']) ?>">Менеджеры</a>
                    <a class="nav-link" href="<?= Url::to(['/admin/context']) ?>">Управление контекстом</a>
                    <a class="nav-link" href="<?= Url::to(['/admin/dispatch']) ?>">Рассылки</a>
                    <a class="nav-link" href="<?= Url::to(['/admin/dispatch-pushes']) ?>">Информационные рассылки</a>
                    <a class="nav-link" href="<?= Url::to(['/admin/settings']) ?>">Статический контент</a>
                    <a class="nav-link" href="<?= Url::to(['/admin/user-quiz']) ?>">Анкеты</a>
                    <div class="sb-sidenav-menu-heading">Контент</div>
                    <a class="nav-link" href="<?= Url::to(['/admin/structure-catalog']) ?>">Группы продуктов</a>
                    <a class="nav-link" href="<?= Url::to(['/admin/product']) ?>">Продукты</a>
                    <a class="nav-link" href="<?= Url::to(['/admin/groups-example']) ?>">Подбор продуктов</a>
                    <a class="nav-link" href="<?= Url::to(['/admin/holiday']) ?>">Праздники</a>
                    <a class="nav-link" href="<?= Url::to(['/admin/holiday-example']) ?>">Подбор праздника</a>
                    <a class="nav-link" href="<?= Url::to(['/admin/image']) ?>">Образы</a>
                    <a class="nav-link" href="<?= Url::to(['/admin/image-products']) ?>">Продукты к образам</a>
                    <a class="nav-link" href="<?= Url::to(['/admin/unwantedinformation']) ?>">Категории (Нежелат.)</a>
                    <a class="nav-link" href="<?= Url::to(['/admin/unwanted-information-example']) ?>">Нежелательная инфо</a>
                    <a class="nav-link" href="<?= Url::to(['/admin/promotedtopic']) ?>">Продвигаемые темы</a>
                </div>
            </div>
        </nav>
    </div>
    <div id="layoutSidenav_content">
        <main class="container-fluid p-4">
            <div id="content">
                    <?= Breadcrumbs::widget([
                        'homeLink' => ['label'=>'Административная панель', 'url'=>['/admin']],
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]) ?>

                    <?php

                    if (!empty($successFlash)) {
		echo Alert::widget([
		    'options' => [
		        'class' => 'alert-success',
		    ],
		    'body' => $successFlash,
		]);
	} elseif (!empty($errorFlash)) {
		echo Alert::widget([
		    'options' => [
		        'class' => 'alert-danger',
		    ],
		    'body' => $errorFlash,
		]);
	}
	?>

                    <?= $content ?>
                    </div>
        </main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small text-muted">
                    <div>Время сервера: <?= date('d.m.Y, H:i') ?> (<?= time() ?>)</div>
                    <div>Страница сгенерирована за: <?= round(Yii::getLogger()->elapsedTime, 2).' сек' ?></div>
                </div>
            </div>
        </footer>
    </div>
</div>

<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/StartBootstrap/startbootstrap-sb-admin@master/dist/js/scripts.js"></script>

<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
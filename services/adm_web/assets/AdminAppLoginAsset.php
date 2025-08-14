<?php

namespace app\assets;

use yii\web\AssetBundle;

class AdminAppLoginAsset extends AssetBundle
{
	public $publishOptions = [
	    'forceCopy' => true,
	];

    public $sourcePath = __DIR__.'/../modules/admin/assets';
    public $css = [
    	'css/font-awesome.min.css',
    	'css/login.css',
    ];
    public $js = [
    ];
    public $depends = [
    	'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset'
    ];
}

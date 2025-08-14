<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @method static \yii\web\AssetBundle register($view)
 */
class AdminAppAsset extends AssetBundle
{
	public $publishOptions = [
	    'forceCopy' => true,
	];

    public $sourcePath = __DIR__.'/../modules/admin/assets';
    public $css = [
    	'css/jquery-ui.min.css',
    	'css/bootstrap-switch.min.css',
//    	'css/bootstrap-editable.css',
    	'css/theme.css',

    	'css/admin_project.css',
    ];
    public $js = [
    	'js/jquery-ui.min.js',
    	'js/bootstrap-switch.min.js',
    	'js/bootstrap-editable.js',
    	'js/theme.js',
    ];
    public $depends = [
    	'yii\web\YiiAsset',
    ];
}

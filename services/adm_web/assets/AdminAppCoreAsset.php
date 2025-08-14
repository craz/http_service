<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @method static \yii\web\AssetBundle register($view)
 */
class AdminAppCoreAsset extends AssetBundle
{
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];

    // Отключаем локальные Bootstrap-ассеты: используем CDN в лейауте
    public $sourcePath = null;

    public $css = [];
    public $js = [];
    public $depends = [
        'yii\\web\\YiiAsset',
    ];
}

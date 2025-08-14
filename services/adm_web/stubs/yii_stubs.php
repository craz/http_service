<?php
// Stub classes for IDE/static analysis only. Not loaded at runtime.

namespace yii\db {
    if (!class_exists('yii\\db\\ActiveRecord')) {
        abstract class ActiveRecord {}
    }
}

namespace yii\helpers {
    if (!class_exists('yii\\helpers\\Html')) {
        class Html { public static function encode($s){ return (string)$s; } }
    }
    if (!class_exists('yii\\helpers\\Url')) {
        class Url { public static function to($p){ return ''; } }
    }
}

namespace yii\grid {
    if (!class_exists('yii\\grid\\GridView')) {
        class GridView { public static function widget($c){ return ''; } }
    }
    if (!class_exists('yii\\grid\\ActionColumn')) {
        class ActionColumn {}
    }
}



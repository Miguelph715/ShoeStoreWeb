<?php

declare(strict_types=1);

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Ficheiros CSS/JS do back-office (AdminLTE + o nosso CSS).
 */
class AdminAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/admin.css',
    ];
    public $depends = [
        'hail812\adminlte3\assets\AdminLteAsset',
        'hail812\adminlte3\assets\FontAwesomeAsset',
    ];
}
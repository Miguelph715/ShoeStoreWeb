<?php

declare(strict_types=1);

namespace frontend\assets;

use hail812\adminlte3\assets\FontAwesomeAsset;
use yii\bootstrap5\BootstrapAsset;
use yii\bootstrap5\BootstrapPluginAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

/**
 * Ficheiros CSS/JS do front-office (loja).
 * Os icones vem do Font Awesome que o AdminLTE ja instalou no vendor.
 * O BootstrapPluginAsset e o JavaScript do Bootstrap (menu lateral, menu de telemovel).
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/shop.css',
    ];
    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
        BootstrapPluginAsset::class,
        FontAwesomeAsset::class,
    ];
}
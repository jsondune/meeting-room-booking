<?php
/**
 * Backend Application Asset Bundle
 */

namespace backend\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $css = [
        // Custom CSS files can be added here
    ];
    
    public $js = [
        // Custom JS files can be added here
    ];
    
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
        'yii\bootstrap5\BootstrapPluginAsset',
    ];
}

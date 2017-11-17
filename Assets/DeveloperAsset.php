<?php

namespace Iliich246\YicmsCommon\Assets;

use yii\web\AssetBundle;

/**
 * Developer asset bundle
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class DeveloperAsset extends AssetBundle
{
    public $sourcePath = '@yicms-common/Assets/developer';

    public $css = [
        'developer.css',
    ];

    public $js = [
        //'developer.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}

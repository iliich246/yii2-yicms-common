<?php

namespace Iliich246\YicmsCommon\Assets;

use yii\web\AssetBundle;

/**
 * Class ImagesDevAsset
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ImagesDevAsset extends AssetBundle
{
    public $sourcePath = '@yicms-common/Assets/images-dev';

    public $js = [
        'images-dev.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'yii\widgets\PjaxAsset',
    ];
}

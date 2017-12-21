<?php

namespace Iliich246\YicmsCommon\Assets;

use yii\web\AssetBundle;

/**
 * Class FreeEssencesDevAsset
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FreeEssencesDevAsset extends AssetBundle
{
    public $sourcePath = '@yicms-common/Assets/free-essences-dev';

    public $js = [
        'free-essences-dev.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'yii\widgets\PjaxAsset',
        'Iliich246\YicmsCommon\Assets\BootboxAsset',
    ];
}

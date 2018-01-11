<?php

namespace Iliich246\YicmsCommon\Assets;

use yii\web\AssetBundle;

/**
 * Class ConditionsDevAsset
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ConditionsDevAsset extends AssetBundle
{
    public $sourcePath = '@yicms-common/Assets/conditions-dev';

    public $js = [
        'conditions-dev.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'yii\widgets\PjaxAsset',
    ];
}

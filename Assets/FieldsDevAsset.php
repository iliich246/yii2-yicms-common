<?php

namespace Iliich246\YicmsCommon\Assets;

use yii\web\AssetBundle;

/**
 * Class FieldsDevAsset
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FieldsDevAsset extends AssetBundle
{
    public $sourcePath = '@yicms-common/Assets/fields-dev';

    public $js = [
        'fields-dev.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}

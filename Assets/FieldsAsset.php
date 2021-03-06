<?php

namespace Iliich246\YicmsCommon\Assets;

use yii\web\AssetBundle;

/**
 * Class FieldsAsset
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FieldsAsset extends AssetBundle
{
    public $sourcePath = '@yicms-common/Assets/fields';

    public $js = [
        'fields.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'yii\widgets\PjaxAsset',
        'Iliich246\YicmsCommon\Assets\BootboxAsset',
    ];
}

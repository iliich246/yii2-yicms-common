<?php

namespace Iliich246\YicmsCommon\Assets;

use yii\web\AssetBundle;

/**
 * Class FilesDevAsset
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FilesDevAsset extends AssetBundle
{
    public $sourcePath = '@yicms-common/Assets/files-dev';

    public $js = [
        'files-dev.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'yii\widgets\PjaxAsset',
    ];
}

<?php

namespace Iliich246\YicmsCommon\Assets;

use yii\web\AssetBundle;

/**
 * Bootbox asset bundle
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class BootboxAsset extends AssetBundle
{
    public $sourcePath = '@npm/bootbox';

    public $js = [
        YII_ENV_DEV ? 'bootbox.js' :
            'bootbox.min.js'
    ];
}

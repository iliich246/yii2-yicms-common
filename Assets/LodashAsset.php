<?php

namespace Iliich246\YicmsCommon\Assets;

use yii\web\AssetBundle;

/**
 * Lodash asset bundle
 *
 * @package Iliich246\YicmsCommon\Assets
 */
class LodashAsset extends AssetBundle
{
    public $sourcePath = '@npm/lodash';

    public $js = [
        YII_ENV_DEV ? 'lodash.js' :
            'lodash.min.js'
    ];
}

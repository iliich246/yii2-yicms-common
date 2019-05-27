<?php

namespace Iliich246\YicmsCommon\Assets;

use yii\web\AssetBundle;

/**
 * Lodash asset bundle
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class LodashAsset extends AssetBundle
{
    public $sourcePath = '@npm/lodash';

    public $js = [
        YII_ENV_DEV ? 'lodash.js' :
            'lodash.min.js'
    ];
}

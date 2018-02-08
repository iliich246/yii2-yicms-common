<?php

namespace Iliich246\YicmsCommon\Assets;

use yii\web\AssetBundle;

/**
 * Class JsColorPickerAsset
 *
 * Asset bundle class for https://github.com/jo/JSColor
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class JsColorPickerAsset extends AssetBundle
{
    public $sourcePath = '@npm/jscolor-picker';

    public $js = [
        YII_ENV_DEV ? 'jscolor.js' :
            'jscolor.min.js'
    ];
}

<?php

namespace Iliich246\YicmsCommon\Assets;

use yii\web\AssetBundle;

/**
 * Class CropperAsset
 *
 * Asset bundle class for https://github.com/fengyuanchen/cropper
 *
 * @package Iliich246\YicmsCommon\Assets
 */
class CropperAsset extends AssetBundle
{
    public $sourcePath = '@npm/cropper/dist';

    public $css = [
        YII_ENV_DEV ? 'cropper.css' :
            'cropper.min.css'
    ];

    public $js = [
        YII_ENV_DEV ? 'cropper.js' :
            'cropper.min.js'
    ];
}

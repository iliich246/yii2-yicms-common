<?php

namespace Iliich246\YicmsCommon\Assets;

use yii\web\AssetBundle;

/**
 * Class PhotoSwipeAsset
 *
 * Asset bundle class for https://github.com/dimsemenov/PhotoSwipe
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class PhotoSwipeAsset extends AssetBundle
{
    public $sourcePath = '@npm/photoswipe/dist';

    public $css = [
        'photoswipe.css'
    ];

    public $js = [
        YII_ENV_DEV ? 'photoswipe.js' :
            'photoswipe.min.js',
        YII_ENV_DEV ? 'photoswipe-ui-default.js' :
            'photoswipe-ui-default.min.js',
    ];
}

<?php

namespace Iliich246\YicmsCommon\Assets;

use yii\web\AssetBundle;

/**
 * LinkWithConfirmation asset bundle (for LinkWithConfirmationWidget)
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class LinkWithConfirmationAsset extends AssetBundle
{
    public $sourcePath = '@yicms-common/Assets/link-with-confirmation';

    public $js = [
        'bootbox-link.js',
    ];

    public $depends = [
        'Iliich246\YicmsCommon\Assets\DeveloperAsset',
        'yii\widgets\ActiveFormAsset',
        'yii\widgets\PjaxAsset',
    ];
}

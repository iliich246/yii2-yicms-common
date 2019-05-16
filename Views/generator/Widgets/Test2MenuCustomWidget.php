<?php

namespace app\yicms\Common\Widgets;

use yii\bootstrap\Widget;

/**
 * Class TestMenuCustomWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class Test2MenuCustomWidget extends Widget
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('test2_custom', [

        ]);
    }
}

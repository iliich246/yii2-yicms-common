<?php

namespace app\yicms\Common\Widgets;

use yii\bootstrap\Widget;

/**
 * Class TestMenuCustomWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class TestMenuCustomWidget extends Widget
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('test_custom', [

        ]);
    }
}

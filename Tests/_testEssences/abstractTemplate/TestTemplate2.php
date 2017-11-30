<?php

namespace Iliich246\YicmsCommon\Tests\_testEssences\abstractTemplate;

use yii\base\Event;
use Iliich246\YicmsCommon\Base\AbstractTemplate;

/**
 * Class TestTemplate
 *
 * This class is only for test AbstractHandler
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class TestTemplate2 extends AbstractTemplate
{

    protected static $buffer = [];

    public static $accessesToDb = 0;

    /**
     * This method needed for test
     */
    public static function eventToDataFetch2()
    {
        if (self::$accessesToDb != 0) return;

        //TODO: check events error, may be mistake in the framework. Check it out on free time. If this is true, makes issue or fix it.
//      Event::on(TestTemplate2::className(), TestTemplate2::EVENT_BEFORE_FETCH, function($event) {
//          self::$accessesToDb++;
//          \Yii::warning('COUNT = ' . self::$accessesToDb);
//      });
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%test_templates2}}';
    }

    /**
     * @inheritdoc
     */
    protected static function getTemplateReferenceName()
    {
        return 'test_template_reference';
    }
}

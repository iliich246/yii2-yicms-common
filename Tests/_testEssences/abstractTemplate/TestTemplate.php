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
class TestTemplate extends AbstractTemplate
{
    protected static $buffer = [];

    public static $accessesToDb = 0;

    /**
     * This method needed for test
     */
    public static function eventToDataFetch()
    {
        if (self::$accessesToDb != 0) return;

        Event::on(TestTemplate::className(), TestTemplate::EVENT_BEFORE_FETCH, function($event) {
            self::$accessesToDb++;
        });
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%test_templates}}';
    }

    /**
     * @inheritdoc
     */
    protected static function getTemplateReferenceName()
    {
        return 'test_template_reference';
    }
}

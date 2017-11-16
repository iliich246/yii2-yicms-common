<?php

namespace Iliich246\YicmsCommon\Tests\_testEssences\abstractTemplate;

use Iliich246\YicmsCommon\Base\AbstractTemplate;
use yii\base\Event;
use yii\db\ActiveRecord;

/**
 * Class TestTemplate
 *
 * This class is only for test AbstractHandler
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class TestTemplate2 extends AbstractTemplate
{
    private static $buffer = [];

    public static $accessesToDb = 0;

    /**
     * This method needed for test
     */
    public static function eventToDataFetch()
    {
        if (self::$accessesToDb != 0) return;

        Event::on(TestTemplate2::className(), TestTemplate2::EVENT_BEFORE_FETCH, function($event) {
            self::$accessesToDb++;
            \Yii::warning('COUNT = ' . self::$accessesToDb);
        });
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
    protected static function getBuffer()
    {
        return self::$buffer;
    }

    protected static function setBuffer($buffer)
    {
        self::$buffer = $buffer;
    }

    /**
     * @inheritdoc
     */
    protected static function getTemplateReferenceName()
    {
        return 'test_template_reference';
    }
}

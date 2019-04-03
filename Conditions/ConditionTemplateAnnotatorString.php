<?php

namespace Iliich246\YicmsCommon\Conditions;

use yii\base\Component;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Annotations\AnnotatorStringInterface;

/**
 * Class ConditionTemplateAnnotatorString
 *
 * This class needed only for generation annotation string.
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ConditionTemplateAnnotatorString extends Component implements AnnotatorStringInterface
{
    /**
     * @inheritdoc
     * @param ConditionTemplate $searchData
     * @throws CommonException
     */
    public static function getAnnotationsStringArray($searchData)
    {
        $result = [];

        foreach ($searchData->getValuesList() as $value) {
            $result[] =   "   const " . $value->value_name .
                ' = ' . "'" . $value->value_name . "'" .  ";" . PHP_EOL;
        }

        return $result;
    }
}

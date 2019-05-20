<?php

namespace Iliich246\YicmsCommon\Base;

/**
 * Class CommonConfigDb
 *
 * @property string $yicmsLocation
 * @property string $yicmsNamespace
 * @property string $defaultLanguage
 * @property integer $languageMethod
 * @property integer $isGenerated
 * @property integer $strongGenerating
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class CommonConfigDb extends AbstractModuleConfiguratorDb
{
    const SCENARIO_UPDATE = 0x01;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['yicmsLocation', 'yicmsNamespace', 'defaultLanguage'], 'string'],
            [['isGenerated', 'strongGenerating'], 'boolean'],
            ['languageMethod', 'integer']
        ];
    }
}

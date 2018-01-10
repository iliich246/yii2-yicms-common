<?php

namespace Iliich246\YicmsCommon\Conditions;

use yii\db\ActiveRecord;
use Iliich246\YicmsCommon\Languages\LanguagesDb;

/**
 * Class ConditionsNamesTranslatesDb
 *
 * @property integer $id
 * @property integer $common_condition_template_id
 * @property integer $common_language_id
 * @property string $name
 * @property string $description
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ConditionsNamesTranslatesDb extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_conditions_names}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'string', 'max' => '50', 'tooLong' => 'Name of page must be less than 50 symbols'],
            ['description', 'string'],
            [
                ['common_language_id'], 'exist', 'skipOnError' => true,
                'targetClass' => LanguagesDb::className(), 'targetAttribute' => ['common_language_id' => 'id']
            ],
            [
                ['common_condition_template_id'], 'exist', 'skipOnError' => true,
                'targetClass' => ConditionTemplate::className(), 'targetAttribute' => ['common_condition_template_id' => 'id']
            ],
        ];
    }
}

<?php

namespace Iliich246\YicmsCommon\FreeEssences;

use yii\db\ActiveRecord;
use Iliich246\YicmsCommon\Languages\LanguagesDb;

/**
 * Class FieldsNamesTranslatesDb
 *
 * @property integer $id
 * @property integer $common_free_essence_id
 * @property integer $common_language_id
 * @property string $name
 * @property string $description
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FreeEssenceNamesTranslatesDb extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_free_essence_name_translates}}';
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
                ['common_free_essence_id'], 'exist', 'skipOnError' => true,
                'targetClass' => FreeEssences::className(), 'targetAttribute' => ['common_free_essence_id' => 'id']
            ],
        ];
    }
}

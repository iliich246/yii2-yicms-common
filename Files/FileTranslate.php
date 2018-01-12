<?php

namespace Iliich246\YicmsCommon\Files;

use yii\db\ActiveRecord;
use Iliich246\YicmsCommon\Languages\LanguagesDb;

/**
 * Class FileTranslate
 *
 * @property integer $id
 * @property integer $common_file_id
 * @property integer $common_language_id
 * @property integer $system_name
 * @property integer $original_name
 * @property integer $filename
 * @property integer $size
 * @property integer $type
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FileTranslate extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_file_translates}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['common_language_id'], 'exist', 'skipOnError' => true,
                'targetClass' => LanguagesDb::className(), 'targetAttribute' => ['common_language_id' => 'id']
            ],
            [
                ['common_file_id'], 'exist', 'skipOnError' => true,
                'targetClass' => File::className(), 'targetAttribute' => ['common_file_id' => 'id']
            ],
        ];
    }
}

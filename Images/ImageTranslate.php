<?php

namespace Iliich246\YicmsCommon\Images;

use yii\db\ActiveRecord;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Languages\LanguagesDb;

/**
 * Class ImageTranslate
 *
 * @property integer $id
 * @property integer $common_image_id
 * @property integer $common_language_id
 * @property integer $system_name
 * @property integer $original_name
 * @property integer $size
 * @property integer $type
 * @property integer $editable
 * @property integer $visible
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ImageTranslate extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_image_translates}}';
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
                ['common_image_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Image::className(), 'targetAttribute' => ['common_image_id' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        //TODO: implement physical deleting of files

        return parent::delete();
    }

    /**
     * Returns true, if associated file physical existed
     * @return bool
     */
    public function isPhysicalExisted()
    {
        //TODO: implement this method
        return true;
    }
}

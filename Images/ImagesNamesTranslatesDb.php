<?php

namespace Iliich246\YicmsCommon\Images;

use yii\db\ActiveRecord;
use Iliich246\YicmsCommon\Languages\LanguagesDb;

/**
 * Class ImagesNamesTranslatesDb
 *
 * @property integer $id
 * @property integer $common_images_template_id
 * @property integer $common_language_id
 * @property string $name
 * @property string $description
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ImagesNamesTranslatesDb extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_image_names}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'string', 'max' => '50', 'tooLong' => 'Name of image block must be less than 50 symbols'],
            ['description', 'string'],
            [
                ['common_language_id'], 'exist', 'skipOnError' => true,
                'targetClass' => LanguagesDb::className(), 'targetAttribute' => ['common_language_id' => 'id']
            ],
            [
                ['common_images_template_id'], 'exist', 'skipOnError' => true,
                'targetClass' => ImagesBlock::className(), 'targetAttribute' => ['common_images_template_id' => 'id']
            ],
        ];
    }
}

<?php

namespace Iliich246\YicmsCommon\FreeEssences;

use yii\db\ActiveRecord;

/**
 * Class FreeEssences
 *
 * @property integer $id
 * @property string $program_name
 * @property bool $editable
 * @property bool $visible
 * @property bool $free_essences_order
 * @property string $field_template_reference
 * @property string $field_reference
 * @property string $file_template_reference
 * @property string $file_reference
 * @property string $image_template_reference
 * @property string $image_reference
 * @property string $condition_template_reference
 * @property string $condition_reference
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FreeEssences extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_free_essences}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

        ];
    }
}

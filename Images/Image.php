<?php

namespace Iliich246\YicmsCommon\Images;

use Iliich246\YicmsCommon\Base\AbstractEntity;

/**
 * Class Image
 *
 * @property integer $id
 * @property integer $common_images_templates_id
 * @property integer $image_reference
 * @property integer $field_reference
 * @property integer $system_name
 * @property integer $original_name
 * @property integer $size
 * @property integer $type
 * @property integer $editable
 * @property integer $visible
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class Image extends AbstractEntity
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_images}}';
    }

    /**
     * @inheritdoc
     */
    protected static function getReferenceName()
    {
        return 'image_reference';
    }
}

<?php

namespace Iliich246\YicmsCommon\Files;

use yii\behaviors\TimestampBehavior;
use Iliich246\YicmsCommon\Base\AbstractEntity;
use Iliich246\YicmsCommon\Base\SortOrderInterface;
use Iliich246\YicmsCommon\Base\SortOrderTrait;

/**
 * Class File
 *
 * @property string $common_files_template_id
 * @property string $file_reference
 * @property string $field_reference
 * @property string $system_name
 * @property string $original_name
 * @property integer $file_order
 * @property integer $size
 * @property string $type
 * @property bool $editable
 * @property bool $visible
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class File extends AbstractEntity implements SortOrderInterface
{
    use SortOrderTrait;

    public $file;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_files}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function getFileBlock()
    {
        return $this->entityBlock;
    }

    /**
     * @inheritdoc
     */
    public function getOrderQuery()
    {
        return self::find()->where([
            'common_files_template_id' => $this->common_files_template_id,
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function getOrderFieldName()
    {
        return 'file_order';
    }

    /**
     * @inheritdoc
     */
    public function getOrderValue()
    {
        return $this->file_order;
    }

    /**
     * @inheritdoc
     */
    public function setOrderValue($value)
    {
        $this->file_order = $value;
    }

    /**
     * @inheritdoc
     */
    public function configToChangeOfOrder()
    {
        //$this->scenario = self::SCENARIO_CHANGE_ORDER;
    }

    /**
     * @inheritdoc
     */
    public function getOrderAble()
    {
        return $this;
    }
}

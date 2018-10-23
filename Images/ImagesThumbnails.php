<?php

namespace Iliich246\YicmsCommon\Images;

use yii\db\ActiveRecord;

/**
 * Class ImagesThumbnails
 *
 * @property integer $id
 * @property integer $common_images_templates_id
 * @property string $program_name
 * @property integer $divider
 * @property integer $quality
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ImagesThumbnails extends ActiveRecord
{
    const SCENARIO_CREATE = 0x00;
    const SCENARIO_UPDATE = 0x01;

    /**
     * @var ImagesBlock instance associated with this thumbnail
     */
    private $imagesBlock;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_images_thumbnails}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'program_name' => 'Program name',
            'divider'      => 'Divider (digit 1 to infinity)',
            'quality'      => 'Quality (in percents)'
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'program_name', 'divider', 'quality'
            ],
            self::SCENARIO_UPDATE => [
                'program_name', 'divider', 'quality'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->scenario == self::SCENARIO_CREATE)
            $this->common_images_templates_id = $this->imagesBlock->id;

        return parent::save($runValidation = true, $attributeNames = null);
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        parent::delete();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['divider', 'integer', 'min' => 1, 'tooSmall' => 'Value can`t be less than 1'],
            [
                'quality', 'integer',
                'min' => 1, 'tooSmall' => 'Value can`t be less than 1',
                'max' => 100, 'tooBig' => 'Value can`t be more than 100'
            ],
            ['program_name', 'required', 'message' => 'Obligatory input field'],
            ['program_name', 'string', 'max' => '50', 'tooLong' => 'Program name must be less than 50 symbols'],
            ['program_name', 'validateProgramName'],
        ];
    }

    /**
     * Validates the program name.
     * This method checks, that for group images thumbnails program name is unique.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateProgramName($attribute, $params)
    {
        if (!$this->hasErrors()) {

            $query = static::find()->where([
                'common_images_templates_id' => $this->common_images_templates_id,
                'program_name'               => $this->program_name
            ]);

            if ($this->scenario == self::SCENARIO_UPDATE)
                $query->andWhere(['not in', 'program_name', $this->getOldAttribute('program_name')]);

            $count = $query->all();

            if ($count)$this->addError($attribute, 'Thumbnail with same name already existed');
        }
    }

    /**
     * Images Block setter
     * @param ImagesBlock $imagesBlock
     */
    public function setImagesBlock(ImagesBlock $imagesBlock)
    {
        $this->imagesBlock = $imagesBlock;
    }

    //public static function getInstance($template)
}

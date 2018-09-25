<?php

namespace Iliich246\YicmsCommon\FreeEssences;

use Yii;
use yii\db\ActiveRecord;
use Iliich246\YicmsCommon\Base\SortOrderTrait;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Base\FictiveInterface;
use Iliich246\YicmsCommon\Base\SortOrderInterface;
use Iliich246\YicmsCommon\Fields\FieldsHandler;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\FieldsInterface;
use Iliich246\YicmsCommon\Fields\FieldReferenceInterface;
use Iliich246\YicmsCommon\Files\FilesBlock;
use Iliich246\YicmsCommon\Files\FilesHandler;
use Iliich246\YicmsCommon\Files\FilesInterface;
use Iliich246\YicmsCommon\Files\FilesReferenceInterface;
use Iliich246\YicmsCommon\Images\ImagesBlock;
use Iliich246\YicmsCommon\Images\ImagesHandler;
use Iliich246\YicmsCommon\Images\ImagesInterface;
use Iliich246\YicmsCommon\Images\ImagesReferenceInterface;
use Iliich246\YicmsCommon\Conditions\ConditionTemplate;
use Iliich246\YicmsCommon\Conditions\ConditionsHandler;
use Iliich246\YicmsCommon\Conditions\ConditionsInterface;
use Iliich246\YicmsCommon\Conditions\ConditionsReferenceInterface;

/**
 * Class FreeEssences
 *
 * @property integer $id
 * @property string $program_name
 * @property boolean $editable
 * @property boolean $visible
 * @property integer $free_essences_order
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
class FreeEssences extends ActiveRecord implements
    FieldsInterface,
    FieldReferenceInterface,
    FilesInterface,
    FilesReferenceInterface,
    ImagesInterface,
    ImagesReferenceInterface,
    ConditionsReferenceInterface,
    ConditionsInterface,
    FictiveInterface,
    SortOrderInterface
{
    use SortOrderTrait;

    const SCENARIO_CREATE = 0;
    const SCENARIO_UPDATE = 1;

    /** @var FieldsHandler instance of field handler object */
    private $fieldHandler;
    /** @var FilesHandler */
    private $fileHandler;
    /** @var ImagesHandler */
    private $imageHandler;
    /** @var ConditionsHandler */
    private $conditionHandler;

    /**
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->visible = true;
        $this->editable = true;
    }

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
    public function attributeLabels()
    {
        return [
            'program_name' => 'Program name',
            'editable' => 'Editable',
            'visible' => 'Visible',
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'program_name', 'editable', 'visible',
            ],
            self::SCENARIO_UPDATE => [
                'program_name', 'editable', 'visible',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['program_name', 'required', 'message' => 'Obligatory input field'],
            ['program_name', 'string', 'max' => '50', 'tooLong' => 'Program name must be less than 50 symbols'],
            ['program_name', 'validateProgramName'],
        ];
    }

    /**
     * Return instance of page by her name
     * @param $programName
     * @return self
     * @throws CommonException
     */
    public static function getByName($programName)
    {
        /** @var self $freeEssence */
        $freeEssence = self::find()
            ->where(['program_name' => $programName])
            ->one();

        if ($freeEssence) return $freeEssence;

        Yii::error("Сan not find free essence with name " . $programName, __METHOD__);

        if (defined('YICMS_STRICT')) {
            throw new CommonException('Сan not find free essence with name ' . $programName);
        }

        return new self();
    }

    /**
     * Validates the program name.
     * This method serves as the inline validation for page program name.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateProgramName($attribute, $params)
    {
        if (!$this->hasErrors()) {

            $pagesQuery = self::find()->where(['program_name' => $this->program_name]);

            if ($this->scenario == self::SCENARIO_UPDATE)
                $pagesQuery->andWhere(['not in', 'program_name', $this->getOldAttribute('program_name')]);

            $pages = $pagesQuery->all();
            if ($pages)$this->addError($attribute, 'Free essence with same name already exist in system');
        }
    }

    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
        if ($this->hasErrors()) return;

        if ($this->scenario == self::SCENARIO_CREATE) {
            $this->field_template_reference = FieldTemplate::generateTemplateReference();
            $this->field_reference = $this->field_template_reference;

            $this->file_template_reference = FilesBlock::generateTemplateReference();
            $this->file_reference = $this->file_template_reference;

            $this->image_template_reference = ImagesBlock::generateTemplateReference();
            $this->image_reference = $this->image_template_reference;

            $this->condition_template_reference = ConditionTemplate::generateTemplateReference();
            $this->condition_reference = $this->condition_template_reference;
        }
    }

    /**
     * @inheritdoc
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->scenario === self::SCENARIO_CREATE) {
            $this->free_essences_order = $this->maxOrder();
        }

        return parent::save($runValidation = true, $attributeNames = null);
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        /** @var FieldTemplate[] $fieldTemplates */
        $fieldTemplates = FieldTemplate::find()->where([
            'field_template_reference' => $this->getFieldTemplateReference(),
        ])->all();

        foreach($fieldTemplates as $fieldTemplate)
            $fieldTemplate->delete();

        /** @var FilesBlock[] $filesBlocks */
        $filesBlocks = FilesBlock::find()->where([
            'file_template_reference' => $this->getFileTemplateReference(),
        ])->all();

        foreach($filesBlocks as $fileBlock)
            $fileBlock->delete();

        /** @var ImagesBlock[] $imageBlocks */
        $imageBlocks = ImagesBlock::find()->where([
            'image_template_reference' => $this->getImageTemplateReference(),
        ])->all();

        foreach($imageBlocks as $imageBlock)
            $imageBlock->delete();

        /** @var ConditionTemplate[] $conditionTemplates */
        $conditionTemplates = ConditionTemplate::find()->where([
            'condition_template_reference' => $this->getConditionTemplateReference(),
        ])->all();

        foreach($conditionTemplates as $conditionTemplate)
            $conditionTemplate->delete();

        /** @var FreeEssenceNamesTranslatesDb[] $freeEssenceNames */
        $freeEssenceNames = FreeEssenceNamesTranslatesDb::find()->where([
            'common_free_essence_id' => $this->id,
        ])->all();

        foreach($freeEssenceNames as $freeEssenceName)
            $freeEssenceName->delete();

        return parent::delete();
    }

    /**
     * Return true if free essence has any constraints
     * @return bool
     */
    public function isConstraints()
    {
        /** @var FieldTemplate[] $fieldTemplates */
        $fieldTemplates = FieldTemplate::find()->where([
            'field_template_reference' => $this->getFieldTemplateReference(),
        ])->all();

        foreach($fieldTemplates as $fieldTemplate)
            if ($fieldTemplate->isConstraints()) return true;

        /** @var FilesBlock[] $filesBlocks */
        $filesBlocks = FilesBlock::find()->where([
            'file_template_reference' => $this->getFileTemplateReference(),
        ])->all();

        foreach($filesBlocks as $fileBlock)
            if ($fileBlock->isConstraints()) return true;

        /** @var ImagesBlock[] $imageBlocks */
        $imageBlocks = ImagesBlock::find()->where([
            'image_template_reference' => $this->getImageTemplateReference(),
        ])->all();

        foreach($imageBlocks as $imageBlock)
            if ($imageBlock->isConstraints()) return true;

        /** @var ConditionTemplate[] $conditionTemplates */
        $conditionTemplates = ConditionTemplate::find()->where([
            'condition_template_reference' => $this->getConditionTemplateReference(),
        ])->all();

        foreach($conditionTemplates as $conditionTemplate)
            if ($conditionTemplate->isConstraints()) return true;

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getFieldHandler()
    {
        if (!$this->fieldHandler)
            $this->fieldHandler = new FieldsHandler($this);

        return $this->fieldHandler;
    }

    /**
     * @inheritdoc
     */
    public function getField($name)
    {
        return $this->getFieldHandler()->getField($name);
    }

    /**
     * @inheritdoc
     */
    public function getFieldTemplateReference()
    {
        return $this->field_template_reference;
    }

    /**
     * @inheritdoc
     */
    public function getFieldReference()
    {
        return $this->field_reference;
    }

    /**
     * @inheritdoc
     */
    public function getFileHandler()
    {
        if (!$this->fileHandler)
            $this->fileHandler = new FilesHandler($this);

        return $this->fileHandler;
    }

    /**
     * @inheritdoc
     */
    public function getFileBlock($name)
    {
        return $this->getFileHandler()->getFileBlock($name);
    }

    /**
     * @inheritdoc
     */
    public function getFileTemplateReference()
    {
        return $this->file_template_reference;
    }

    /**
     * @inheritdoc
     */
    public function getFileReference()
    {
        return $this->file_reference;
    }

    /**
     * @inheritdoc
     */
    public function getImagesHandler()
    {
        if (!$this->imageHandler)
            $this->imageHandler = new ImagesHandler($this);

        return $this->imageHandler;
    }

    /**
     * @inheritdoc
     */
    public function getImageBlock($name)
    {
        return $this->getImagesHandler()->getImageBlock($name);
    }

    /**
     * @inheritdoc
     */
    public function getImageTemplateReference()
    {
        return $this->image_template_reference;
    }

    /**
     * @inheritdoc
     */
    public function getImageReference()
    {
        return $this->image_reference;
    }

    /**
     * @inheritdoc
     */
    public function getConditionsHandler()
    {
        if (!$this->conditionHandler)
            $this->conditionHandler = new ConditionsHandler($this);

        return $this->conditionHandler;
    }

    /**
     * @inheritdoc
     */
    public function getCondition($name)
    {
        return $this->getConditionsHandler()->getCondition($name);
    }

    /**
     * @inheritdoc
     */
    public function getConditionTemplateReference()
    {
        return $this->condition_template_reference;
    }

    /**
     * @inheritdoc
     */
    public function getConditionReference()
    {
        return $this->condition_reference;
    }

    /**
     * @inheritdoc
     */
    public function getOrderQuery()
    {
        return self::find();
    }

    /**
     * @inheritdoc
     */
    public static function getOrderFieldName()
    {
        return 'free_essences_order';
    }

    /**
     * @inheritdoc
     */
    public function getOrderValue()
    {
        return $this->free_essences_order;
    }

    /**
     * @inheritdoc
     */
    public function setOrderValue($value)
    {
        $this->free_essences_order = $value;
    }

    /**
     * @inheritdoc
     */
    public function configToChangeOfOrder()
    {
        $this->scenario = self::SCENARIO_UPDATE;
    }

    /**
     * @inheritdoc
     */
    public function getOrderAble()
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setFictive()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function clearFictive()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isFictive()
    {
        return false;
    }
}

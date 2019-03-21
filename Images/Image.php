<?php

namespace Iliich246\YicmsCommon\Images;

use Yii;
use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;
use yii\validators\SafeValidator;
use yii\validators\RequiredValidator;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Annotations\Annotator;
use Iliich246\YicmsCommon\Annotations\AnnotateInterface;
use Iliich246\YicmsCommon\Annotations\AnnotatorFileInterface;
use Iliich246\YicmsCommon\Annotations\AnnotatorStringInterface;
use Iliich246\YicmsCommon\Assets\DeveloperAsset;
use Iliich246\YicmsCommon\Base\AbstractEntity;
use Iliich246\YicmsCommon\Base\SortOrderTrait;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Base\FictiveInterface;
use Iliich246\YicmsCommon\Base\SortOrderInterface;
use Iliich246\YicmsCommon\Languages\Language;
use Iliich246\YicmsCommon\Languages\LanguagesDb;
use Iliich246\YicmsCommon\Fields\Field;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\FieldsHandler;
use Iliich246\YicmsCommon\Fields\FieldsInterface;
use Iliich246\YicmsCommon\Fields\FieldReferenceInterface;
use Iliich246\YicmsCommon\Conditions\Condition;
use Iliich246\YicmsCommon\Conditions\ConditionTemplate;
use Iliich246\YicmsCommon\Conditions\ConditionsHandler;
use Iliich246\YicmsCommon\Conditions\ConditionsInterface;
use Iliich246\YicmsCommon\Conditions\ConditionsReferenceInterface;
use Iliich246\YicmsCommon\Validators\ValidatorBuilder;
use Iliich246\YicmsCommon\Validators\ValidatorBuilderInterface;
use Iliich246\YicmsCommon\Validators\ValidatorReferenceInterface;

/**
 * Class Image
 *
 * @property integer $id
 * @property integer $common_images_templates_id
 * @property string $image_reference
 * @property string $field_reference
 * @property string $condition_reference
 * @property string $system_name
 * @property string $original_name
 * @property integer $image_order
 * @property integer $size
 * @property integer $type
 * @property bool $editable
 * @property bool $visible
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class Image extends AbstractEntity implements
    SortOrderInterface,
    FieldsInterface,
    FieldReferenceInterface,
    ConditionsInterface,
    ConditionsReferenceInterface,
    ValidatorBuilderInterface,
    ValidatorReferenceInterface,
    ImagesProcessorInterface,
    FictiveInterface,
    AnnotateInterface,
    AnnotatorFileInterface,
    AnnotatorStringInterface
{
    use SortOrderTrait;

    const ORIGINALS_MODE = 0;
    const CROPPED_MODE   = 1;

    /** @var UploadedFile loaded image */
    public $image;
    /** @var mixed information about crop */
    public $cropInfo;
    /** @var FieldsHandler instance of field handler object */
    private $fieldHandler;
    /** @var ConditionsHandler instance of condition handler object  */
    private $conditionHandler;
    /** @var ValidatorBuilder instance */
    private $validatorBuilder;
    /** @var ImageTranslate[] array of buffered translates */
    public $imageTranslates;
    /** @var null|string keeps mode of thumbnails */
    private $thumbnailMode = null;
    /** @var null|ImagesThumbnails current active image thumbnail */
    private $currentThumbnail = null;
    /** @var int keep images mode  */
    private $imageMode = self::ORIGINALS_MODE;
    /** @var Annotator instance */
    private $annotator = null;
    /** @var AnnotatorFileInterface instance */
    private static $parentFileAnnotator;
    /** @var array of exception words for magical getter/setter */
    protected static $annotationExceptionWords = [
        'id',
        'common_images_templates_id',
        'image_reference',
        'field_reference',
        'condition_reference',
        'system_name',
        'original_name',
        'image_order',
        'size',
        'type',
        'editable',
        'visible',
        'created_at',
        'updated_at'
    ];

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
    public function attributeLabels()
    {
        return [
            'editable' => 'Editable(dev)'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['editable', 'visible'], 'boolean'],
                ['cropInfo', 'safe']
        ];
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

    /**
     * Magical get method for use object annotations
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (in_array($name, self::$annotationExceptionWords))
            return parent::__get($name);

        if (strpos($name, 'field_') === 0) {
            if ($this->isField(substr($name, 6)))
                return $this->getFieldHandler()->getField(substr($name, 6));

            return parent::__get($name);
        }

        if (strpos($name, 'condition_') === 0) {
            if ($this->isCondition(substr($name, 10)))
                return $this->getConditionsHandler()->getCondition(substr($name, 10));

            return parent::__get($name);
        }

        if ($this->getFieldHandler()->isField($name))
            return $this->getFieldHandler()->getField($name);

        if ($this->getConditionsHandler()->isCondition($name))
            return $this->getConditionsHandler()->getCondition($name);

        return parent::__get($name);
    }

    /**
     * @inheritdoc
     */
    public function getImagesBlock()
    {
        return $this->getEntityBlock();
    }

    /**
     * Proxy parent method with concrete method name
     * @param ImagesBlock $imagesBlock
     */
    public function setImagesBlock(ImagesBlock $imagesBlock)
    {
        $this->setEntityBlock($imagesBlock);
    }

    /**
     * @inheritdoc
     */
    public function getCropInfo()
    {
        return $this->cropInfo;
    }

    /**
     * @inheritdoc
     */
    public function getFileName()
    {
        return $this->system_name;
    }

    /**
     * @inheritdoc
     */
    protected static function getReferenceName()
    {
        return 'image_reference';
    }

    /**
     * @inheritdoc
     * @throws CommonException
     */
    public function getPath(LanguagesDb $language = null)
    {
        if ($this->isNonexistent) return false;

        if (!$language) $language = Language::getInstance()->getCurrentLanguage();

        $imagesBlock = $this->getImagesBlock();

        if ($imagesBlock->language_type == ImagesBlock::LANGUAGE_TYPE_SINGLE)
            $systemName = $this->system_name;
        else {
            $imageTranslate = $this->getImageTranslate($language);

            if (!$imageTranslate) return false;

            $systemName = $imageTranslate->system_name;
        }

        $path = CommonModule::getInstance()->imagesOriginalsPath . $systemName;

        if (!file_exists($path) || is_dir($path)) return false;

        return $path;
    }

    /**
     * Return src param for img tag
     * @param LanguagesDb|null $language
     * @return bool|string
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getSrc(LanguagesDb $language = null)
    {
        if ($this->isNonexistent() && CommonModule::isUnderDev() && defined('YICMS_ALERTS')) {
            $asset = new DeveloperAsset();
            $asset->publish(\Yii::$app->assetManager);

            $src = $asset->baseUrl . '/no-image.png';
            return $src;
        } else if ($this->isNonexistent())
            return '';

        if (!$language) $language = Language::getInstance()->getCurrentLanguage();

        $imagesBlock = $this->getImagesBlock();

        if ($imagesBlock->language_type == ImagesBlock::LANGUAGE_TYPE_SINGLE)
            $systemName = $this->system_name;
        else {
            $imageTranslate = $this->getImageTranslate($language);

            if (!$imageTranslate) return false;

            $systemName = $imageTranslate->system_name;
        }

        if ($this->imageMode == self::ORIGINALS_MODE) {
            if (!is_null($this->thumbnailMode)) {
                $systemName = $this->currentThumbnail->program_name . '_' . $systemName;
                return CommonModule::getInstance()->imagesThumbnailsWebPath . $systemName;
            }

            return CommonModule::getInstance()->imagesOriginalsWebPath . $systemName;
        }

        //CROPPED_MODE
        if (!is_null($this->thumbnailMode))
            $systemName = $this->currentThumbnail->program_name . '_' .$systemName;

        return CommonModule::getInstance()->imagesCropWebPath . $systemName;
    }

    /**
     * Return name of file for admin list
     * @param LanguagesDb|null $language
     * @return bool|int|null|string
     * @throws CommonException
     */
    public function listName(LanguagesDb $language = null)
    {
        if ($this->isNonexistent) return false;

        if (!$language) $language = Language::getInstance()->getCurrentLanguage();

        try {
            $field = $this->getField('name');

            if (!$field) $name = null;
            else {
                $field->setDefaultMode();
                $name = $field->getTranslate($language);
            }
        } catch(CommonException $e) {
            $name = null;
        }

        if ($name || trim($name)) return $name;

        $imagesBlock = $this->getImagesBlock();

        if ($imagesBlock->language_type == ImagesBlock::LANGUAGE_TYPE_SINGLE)
            $name = $this->original_name;
        else {
            $imageTranslate = $this->getImageTranslate($language);

            if (!$imageTranslate) return false;

            $name = $imageTranslate->original_name;
        }

        return $name;
    }

    /**
     * Proxy getSrc() method to magical __toString()
     * @return bool|string
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function __toString()
    {
        return (string)$this->getSrc();
    }

    /**
     * Restore image output mode to default mode
     * @return $this
     * @throws CommonException
     */
    public function setDefaultMode()
    {
        if ($this->getImagesBlock()->crop_type == ImagesBlock::NO_CROP)
            $this->outputOriginal();
        else
            $this->outputCropped();

        $this->disableThumbnail();

        return $this;
    }

    /**
     * Sets image to cropped mode
     * @return $this
     * @throws CommonException
     */
    public function outputCropped()
    {
        if ($this->getImagesBlock()->crop_type == ImagesBlock::NO_CROP) {
            Yii::warning(
                "Can`t set image to output cropped images mode, he has no crop mode",
                __METHOD__);

            if (defined('YICMS_STRICT')) {
                throw new CommonException(
                    "Can`t set image to output cropped images mode, he has no crop mode");
            }

            return $this;
        }

        $this->imageMode = self::CROPPED_MODE;

        return $this;
    }

    /**
     * Sets image to original mode
     * @return $this
     */
    public function outputOriginal()
    {
        $this->imageMode = self::ORIGINALS_MODE;

        return $this;
    }

    /**
     * Sets original to output thumbnails mode
     * @param $name
     * @return $this
     * @throws CommonException
     */
    public function outputThumbnail($name)
    {
        /** @var ImagesThumbnails $thumbnail */
        $thumbnail = ImagesThumbnails::find()->where([
            'common_images_templates_id' => $this->getImagesBlock()->id,
            'program_name'               => $name
        ])->one();

        if (!$thumbnail) {
            Yii::warning(
                "Can`t find thumbnail mode $name",
                __METHOD__);

            if (defined('YICMS_STRICT')) {
                throw new CommonException(
                    "Can`t find thumbnail mode $name");
            }

            if (is_null($this->thumbnailMode)) $this->thumbnailMode = null;

            return $this;
        }

        $this->thumbnailMode    = $name;
        $this->currentThumbnail = $thumbnail;

        return $this;
    }

    /**
     * Disable image thumbnails mode
     * @return $this
     */
    public function disableThumbnail()
    {
        $this->thumbnailMode = null;
        $this->thumbnailMode = null;

        return $this;
    }

    /**
     * Returns buffered image translate db
     * @param LanguagesDb $language
     * @return ImageTranslate
     */
    private function getImageTranslate(LanguagesDb $language)
    {
        if (!isset($this->imageTranslates[$language->id]) &&
        !is_null($this->imageTranslates[$language->id])) {
            $this->imageTranslates[$language->id] = ImageTranslate::find()->where([
                'common_image_id'    => $this->id,
                'common_language_id' => $language->id
            ])->one();
        }

        return $this->imageTranslates[$language->id];
    }

    /**
     * @inheritdoc
     */
    public function entityBlockQuery()
    {
        return ImagesBlock::find()->where([
            'id' => $this->common_images_templates_id
        ]);
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        $this->deleteSequence();
        return parent::delete();
    }

    /**
     * @inheritdoc
     * @throws CommonException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    protected function deleteSequence()
    {
        $imageTranslates = ImageTranslate::find()->where([
            'common_image_id' => $this->id,
        ])->all();

        if ($imageTranslates)
            foreach($imageTranslates as $imageTranslate)
                $imageTranslate->delete();

        //TODO: add physical files deleting

        /** @var ImagesBlock $imageBlock */
        $imageBlock = $this->getEntityBlock();

        /** @var FieldTemplate $fieldTemplates */
        $fieldTemplates = FieldTemplate::find()->where([
            'field_template_reference' => $imageBlock->getFieldTemplateReference(),
        ])->all();

        foreach($fieldTemplates as $fieldTemplate) {
            /** @var Field $field */
            $field = Field::find()->where([
                'common_fields_template_id' => $fieldTemplate->id,
                'field_reference'           => $this->field_reference,
            ])->one();

            if ($field) $field->delete();
        }

        /** @var ConditionTemplate $conditionTemplates */
        $conditionTemplates = ConditionTemplate::find()->where([
            'condition_template_reference' => $imageBlock->getConditionTemplateReference(),
        ])->all();

        foreach($conditionTemplates as $conditionTemplate) {
            /** @var Condition $condition */
            $condition = Condition::find()->where([
                'common_condition_template_id' => $conditionTemplate->id,
                'condition_reference'          => $this->condition_reference
            ])->one();

            if ($condition) $condition->delete();
        }

        return true;
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
    public function isField($name)
    {
        return $this->getFieldHandler()->isField($name);
    }

    /**
     * @inheritdoc
     * @throws CommonException
     */
    public function getFieldTemplateReference()
    {
        return $this->getImagesBlock()->getFieldTemplateReference();
    }

    /**
     * @inheritdoc
     * @throws CommonException
     */
    public function getFieldReference()
    {
        if (!$this->field_reference) {
            $this->field_reference = Field::generateReference();
            $this->save(false);
        }

        return $this->field_reference;
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
    public function isCondition($name)
    {
        return $this->getConditionsHandler()->isCondition($name);
    }

    /**
     * @inheritdoc
     * @throws CommonException
     */
    public function getConditionTemplateReference()
    {
        return $this->getImagesBlock()->getConditionTemplateReference();
    }

    /**
     * @inheritdoc
     * @throws CommonException
     */
    public function getConditionReference()
    {
        if (!$this->condition_reference) {
            $this->condition_reference = ConditionTemplate::generateTemplateReference();
            $this->save(false);
        }

        return $this->condition_reference;
    }

    /**
     * Method config validators for this model
     * @throws CommonException
     * @return void
     */
    public function prepareValidators()
    {
        $validators = $this->getValidatorBuilder()->build();

        if (!$validators) {

            $safeValidator = new SafeValidator();
            $safeValidator->attributes = ['image'];
            $this->validators[] = $safeValidator;

            return;
        }

        foreach ($validators as $validator) {

            if ($validator instanceof RequiredValidator && !$this->isNewRecord) continue;

            $validator->attributes = ['image'];
            $this->validators[] = $validator;
        }
    }

    /**
     * @inheritdoc
     */
    public function getValidatorBuilder()
    {
        if ($this->validatorBuilder) return $this->validatorBuilder;

        $this->validatorBuilder = new ValidatorBuilder();
        $this->validatorBuilder->setReferenceAble($this);

        return $this->validatorBuilder;
    }

    /**
     * @inheritdoc
     * @throws CommonException
     */
    public function getValidatorReference()
    {
        $imageBlock = $this->getImagesBlock();

        if (!$imageBlock->validator_reference) {
            $imageBlock->validator_reference = ValidatorBuilder::generateValidatorReference();
            $imageBlock->scenario = ImagesBlock::SCENARIO_UPDATE;
            $imageBlock->save(false);
        }

        return $imageBlock->validator_reference;
    }

    /**
     * @inheritdoc
     */
    public function getOrderQuery()
    {
        return self::find()->where([
            'common_images_templates_id' => $this->common_images_templates_id,
            'image_reference'            => $this->image_reference,
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function getOrderFieldName()
    {
        return 'image_order';
    }

    /**
     * @inheritdoc
     */
    public function getOrderValue()
    {
        return $this->image_order;
    }

    /**
     * @inheritdoc
     */
    public function setOrderValue($value)
    {
        $this->image_order = $value;
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

    /**
     * @inheritdoc
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     * @throws \ReflectionException
     */
    public function annotate()
    {
        $this->getAnnotator()->addAnnotationArray(
            FieldTemplate::getAnnotationsStringArray($this->getFieldTemplateReference())
        );
    }

    /**
     * @inheritdoc
     * @throws \ReflectionException
     */
    public function getAnnotator()
    {
        if (!is_null($this->annotator)) return $this->annotator;

        $this->annotator = new Annotator();
        $this->annotator->setAnnotatorFileObject($this);
        $this->annotator->prepare();

        return $this->annotator;
    }

    /**
     * Sets parent file annotator
     * @param AnnotatorFileInterface $fileAnnotator
     */
    public static function setParentFileAnnotator(AnnotatorFileInterface $fileAnnotator)
    {
        self::$parentFileAnnotator = $fileAnnotator;
    }

    /**
     * @inheritdoc
     */
    public function getAnnotationFileName()
    {
        return ucfirst(mb_strtolower($this->getImagesBlock()->program_name)) . 'Image';
    }

    /**
     * @inheritdoc
     */
    public function getAnnotationFilePath()
    {
        if (!is_dir(self::$parentFileAnnotator->getAnnotationFilePath() . '/' .
            self::$parentFileAnnotator->getAnnotationFileName()))
            mkdir(self::$parentFileAnnotator->getAnnotationFilePath() . '/' .
                self::$parentFileAnnotator->getAnnotationFileName());

        return self::$parentFileAnnotator->getAnnotationFilePath() . '/' .
            self::$parentFileAnnotator->getAnnotationFileName() . '/Images';
    }

    /**
     * @inheritdoc
     */
    public function getExtendsUseClass()
    {
        return 'Iliich246\YicmsCommon\Images\Image';
    }

    /**
     * @inheritdoc
     */
    public function getExtendsClassName()
    {
        return 'Image';
    }

    /**
     * @inheritdoc
     * @throws \ReflectionException
     */
    public static function getAnnotationTemplateFile()
    {
        $class = new \ReflectionClass(self::class);
        return dirname($class->getFileName())  . '/annotations/image.php';
    }

    /**
     * @inheritdoc
     */
    public static function getAnnotationFileNamespace()
    {
        return self::$parentFileAnnotator->getAnnotationFileNamespace() . '\\'
            . self::$parentFileAnnotator->getAnnotationFileName() . '\\'
            . 'Images';
    }

    /**
     * @inheritdoc
     */
    public static function getAnnotationsStringArray($data)
    {

    }
}

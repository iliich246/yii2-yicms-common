<?php

namespace Iliich246\YicmsCommon\Images;

use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\AbstractEntity;
use Iliich246\YicmsCommon\Base\SortOrderTrait;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Base\SortOrderInterface;
use Iliich246\YicmsCommon\Languages\Language;
use Iliich246\YicmsCommon\Languages\LanguagesDb;
use Iliich246\YicmsCommon\Fields\Field;
use Iliich246\YicmsCommon\Fields\FieldsHandler;
use Iliich246\YicmsCommon\Fields\FieldsInterface;
use Iliich246\YicmsCommon\Fields\FieldReferenceInterface;
use Iliich246\YicmsCommon\Validators\ValidatorBuilder;
use Iliich246\YicmsCommon\Validators\ValidatorBuilderInterface;
use Iliich246\YicmsCommon\Validators\ValidatorReferenceInterface;

/**
 * Class Image
 *
 * @property integer $id
 * @property integer $common_images_templates_id
 * @property integer $image_reference
 * @property integer $field_reference
 * @property integer $system_name
 * @property integer $original_name
 * @property integer $image_order
 * @property integer $size
 * @property integer $type
 * @property integer $editable
 * @property integer $visible
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class Image extends AbstractEntity implements
    SortOrderInterface,
    FieldsInterface,
    FieldReferenceInterface,
    ValidatorBuilderInterface,
    ValidatorReferenceInterface
{
    use SortOrderTrait;

    /**
     * @var UploadedFile loaded image
     */
    public $image;
    /**
     * @var mixed information about crop
     */
    public $cropInfo;
    /**
     * @var FieldsHandler instance of field handler object
     */
    private $fieldHandler;
    /**
     * @var ValidatorBuilder instance
     */
    private $validatorBuilder;
    /**
     * @var ImageTranslate[] array of buffered translates
     */
    public $imageTranslates;

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
            [['editable', 'visible'], 'boolean']
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
     * Returns ImagesBlock associated with this file entity
     * @return ImagesBlock
     */
    public function getImagesBlock()
    {
        return $this->getEntityBlock();
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

        $path = CommonModule::getInstance()->imagesWebPath .
            '/orig/' . $systemName;

        //if (!file_exists($path) || is_dir($path)) return false;

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

        $path = CommonModule::getInstance()->imagesWebPath .
            '/orig/' . $systemName;

        //if (!file_exists($path) || is_dir($path)) return false;

        return $path;
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
     * Returns buffered image translate db
     * @param LanguagesDb $language
     * @return ImageTranslate
     */
    private function getImageTranslate(LanguagesDb $language)
    {
        if (isset($this->imageTranslates[$language->id])) return $this->imageTranslates[$language->id];

        $this->imageTranslates[$language->id] = ImageTranslate::find()->where([
            'common_image_id' => $this->id,
            'common_language_id' => $language->id
        ])->one();

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
        return $this->getImagesBlock()->getFieldTemplateReference();
    }

    /**
     * @inheritdoc
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
     * Method config validators for this model
     * @return void
     */
    public function prepareValidators()
    {

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
     */
    public function getValidatorReference()
    {
        $fileBlock = $this->getImagesBlock();

        if (!$fileBlock->validator_reference) {
            $fileBlock->validator_reference = ValidatorBuilder::generateValidatorReference();
            $fileBlock->scenario = ImagesBlock::SCENARIO_UPDATE;
            $fileBlock->save(false);
        }

        return $fileBlock->validator_reference;
    }

    /**
     * @inheritdoc
     */
    public function getOrderQuery()
    {
        return self::find()->where([
            'common_images_templates_id' => $this->common_images_templates_id,
            'image_reference' => $this->image_reference,
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
}

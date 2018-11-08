<?php

namespace Iliich246\YicmsCommon\Images;

use yii\db\ActiveQuery;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Base\AbstractEntityBlock;
use Iliich246\YicmsCommon\Languages\Language;
use Iliich246\YicmsCommon\Languages\LanguagesDb;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Fields\FieldReferenceInterface;
use Iliich246\YicmsCommon\Conditions\ConditionTemplate;
use Iliich246\YicmsCommon\Conditions\ConditionsReferenceInterface;
use Iliich246\YicmsCommon\Validators\ValidatorDb;
use Iliich246\YicmsCommon\Validators\ValidatorBuilder;
use Iliich246\YicmsCommon\Validators\ValidatorReferenceInterface;

/**
 * Class ImagesBlock
 *
 * @property string $image_template_reference
 * @property string $field_template_reference
 * @property string $condition_template_reference
 * @property string $validator_reference
 * @property integer $type
 * @property integer $language_type
 * @property integer $image_order
 * @property bool $visible
 * @property bool $editable
 * @property integer $max_images
 * @property string $fill_color
 * @property integer $crop_type
 * @property integer $crop_height
 * @property integer $crop_width
 *
 * @method Image getEntity()
 * @method Image[] getEntities()
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ImagesBlock extends AbstractEntityBlock implements
    FieldReferenceInterface,
    ValidatorReferenceInterface,
    ConditionsReferenceInterface
{
    /**
     * Images types
     */
    const TYPE_MULTIPLICITY = 0;
    const TYPE_ONE_IMAGE    = 1;
    /**
     * Language types of images
     * Type define is image have translates or image has one instance independent of languages
     */
    const LANGUAGE_TYPE_TRANSLATABLE = 0;
    const LANGUAGE_TYPE_SINGLE       = 1;

    /**
     * Crop types
     */
    const NO_CROP          = 0x00;
    //const CROP_VIEW_MODE_0 = 0x01;
    const CROP_VIEW_MODE_1 = 0x02;
    const CROP_VIEW_MODE_2 = 0x03;
    const CROP_VIEW_MODE_3 = 0x04;

    /** @var bool if true for this block will be created standard images like filename */
    public $createStandardFields = true;
    /** @var ImagesNamesTranslatesDb[] buffer */
    private $imageNamesTranslates = [];
    /** @var string imageReference for what files group must be fetched */
    private $currentImageReference;
    /** @inheritdoc */
    protected static $buffer = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%common_images_templates}}';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->visible  = true;
        $this->editable = true;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),[
            'createStandardFields' => 'Create standard fields (name, alt)',
            'max_images'           => 'Maximum images in block',
            'crop_type'            => 'Crop type',
            'crop_height'          => 'Crop height',
            'crop_width'           => 'Crop width',
            'fill_color'           => "\nFill color",
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['type', 'language_type','crop_type'], 'integer'],
            [['visible', 'editable'], 'boolean'],
            [['max_images', 'crop_height', 'crop_width'], 'integer', 'min' => 0],
            ['fill_color', 'string']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $prevScenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = array_merge($prevScenarios[self::SCENARIO_CREATE],
            [
                'type',
                'language_type',
                'visible',
                'editable',
                'max_images',
                'crop_type',
                'crop_height',
                'crop_width',
                'fill_color'
            ]);
        $scenarios[self::SCENARIO_UPDATE] = array_merge($prevScenarios[self::SCENARIO_UPDATE],
            [
                'type',
                'language_type',
                'visible',
                'editable',
                'max_images',
                'crop_type',
                'crop_height',
                'crop_width',
                'fill_color'
            ]);

        return $scenarios;
    }

    /**
     * Return array of image types
     * @return array|bool
     */
    public static function getTypes()
    {
        static $array = false;

        if ($array) return $array;

        $array = [
            self::TYPE_ONE_IMAGE    => 'One image',
            self::TYPE_MULTIPLICITY => 'Multiple images',
        ];

        return $array;
    }

    /**
     * Return name of type of concrete image
     * @return mixed
     */
    public function getTypeName()
    {
        return self::getTypes()[$this->type];
    }

    /**
     * Return array of field language types
     * @return array|bool
     */
    public static function getLanguageTypes()
    {
        static $array = false;

        if ($array) return $array;

        $array = [
            self::LANGUAGE_TYPE_SINGLE       => 'Single type',
            self::LANGUAGE_TYPE_TRANSLATABLE => 'Translatable type',
        ];

        return $array;
    }

    /**
     * Return name of language type of concrete image
     * @return mixed
     */
    public function getLanguageTypeName()
    {
        return self::getLanguageTypes()[$this->language_type];
    }

    /**
     * Returns array of crop types types
     * @return array|bool
     */
    public static function getCropTypes()
    {
        static $array = false;

        if ($array) return $array;

        $array = [
            self::NO_CROP          => 'No crop',
            //self::CROP_VIEW_MODE_0 => 'Crop viewMode 0',
            self::CROP_VIEW_MODE_1 => 'Crop viewMode 1',
            self::CROP_VIEW_MODE_2 => 'Crop viewMode 2',
            self::CROP_VIEW_MODE_3 => 'Crop viewMode 3',
        ];

        return $array;
    }

    /**
     * Return name of crop type of concrete image block
     * @return mixed
     */
    public function getCropTypeName()
    {
        return self::getCropTypeName()[$this->crop_type];
    }

    /**
     * @inheritdoc
     */
    public function save($runValidation = true, $attributes = null)
    {
        if ($this->scenario === self::SCENARIO_CREATE) {
            $this->image_order = $this->maxOrder();
        }

        return parent::save($runValidation, $attributes);
    }

    /**
     * @inheritdoc
     * @return AbstractEntityBlock|ImagesBlock|null
     * @throws CommonException
     */
    public static function getInstance($templateReference, $programName, $currentImageReference = null)
    {
        /** @var ImagesBlock $value */
        $value = parent::getInstance($templateReference, $programName);

        if (!$value->currentImageReference) $value->currentImageReference = $currentImageReference;

        return $value;
    }

    /**
     * @return bool
     */
    public function isImages()
    {
        return $this->isEntities();
    }

    /**
     * @return int
     */
    public function countImages()
    {
        return $this->countEntities();
    }

    /**
     * @return \Iliich246\YicmsCommon\Base\AbstractEntity[]|Image[]
     */
    public function getImages()
    {
        return $this->getEntities();
    }

    /**
     * @return bool|\Iliich246\YicmsCommon\Base\AbstractEntity|Image
     */
    public function getImage()
    {
        return $this->getEntity();
    }
    
    /**
     * @return bool
     */
    public function isConstraints()
    {
        return true;
    }

    /**
     * Proxy method getSrc to first image in block
     * @param null $language
     * @throws CommonException
     */
    public function getSrc($language = null)
    {
        $this->getImage()->getSrc($language);
    }

    /**
     * Proxy method setDefaultMode to first image in block
     * @return Image
     * @throws CommonException
     */
    public function setDefaultMode()
    {
        return $this->getImage()->setDefaultMode();
    }

    /**
     * Proxy method outputCropped to first image in block
     * @return Image
     * @throws CommonException
     */
    public function outputCropped()
    {
        return $this->getImage()->outputCropped();
    }

    /**
     * Proxy method outputOriginal to first image in block
     * @return Image
     */
    public function outputOriginal()
    {
        return $this->getImage()->outputOriginal();
    }

    /**
     * Proxy method outputThumbnail to first image in block
     * @return Image
     */
    public function outputThumbnail($type)
    {
        return $this->outputThumbnail($type);
    }

    /**
     * Proxy method disableThumbnail to first image in block
     * @return Image
     */
    public function disableThumbnail()
    {
        return $this->getImage()->disableThumbnail();
    }

    /**
     * Sets current image reference
     * @param $imageReference
     */
    public function setImageReference($imageReference)
    {
        $this->currentImageReference = $imageReference;
    }

    /**
     * Returns translated name of image block
     * @param LanguagesDb|null $language
     * @return string
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getName(LanguagesDb $language = null)
    {
        if (!$language) $language = Language::getInstance()->getCurrentLanguage();

        $imageNameTranslate = $this->getImageNameTranslate($language);

        if ($imageNameTranslate && trim($imageNameTranslate->name) && CommonModule::isUnderAdmin())
            return $imageNameTranslate->name;

        if ((!$imageNameTranslate || !trim($imageNameTranslate->name)) && CommonModule::isUnderAdmin())
            return $this->program_name;

        if ($imageNameTranslate && trim($imageNameTranslate->name) && CommonModule::isUnderDev())
            return $imageNameTranslate->name . ' (' . $this->program_name .')';

        if ((!$imageNameTranslate || !trim($imageNameTranslate->name)) && CommonModule::isUnderDev())
            return 'No translate for file block \'' . $this->program_name . '\'';

        return 'Can`t reach this place if all correct';
    }

    /**
     * Returns translated description of image block
     * @param LanguagesDb|null $language
     * @return string
     * @throws \Iliich246\YicmsCommon\Base\CommonException
     */
    public function getDescription(LanguagesDb $language = null)
    {
        if (!$language) $language = Language::getInstance()->getCurrentLanguage();

        $imageNameTranslate = $this->getImageNameTranslate($language);

        if ($imageNameTranslate)
            return $imageNameTranslate->description;

        return false;
    }

    /**
     * Returns buffered name translate db
     * @param LanguagesDb|null $language
     * @return ImagesNamesTranslatesDb
     */
    private function getImageNameTranslate(LanguagesDb $language)
    {
        if (!isset($this->imageNamesTranslates[$language->id])) {

            $data = ImagesNamesTranslatesDb::find()->where([
                'common_images_template_id' => $this->id,
                'common_language_id'        => $language->id,
            ])->one();

            if (!$data) $this->imageNamesTranslates[$language->id] = null;
            else $this->imageNamesTranslates[$language->id] = $data;
        }

        return $this->imageNamesTranslates[$language->id];
    }

    /**
     * Return true if this block has fields
     * @return bool
     * @throws CommonException
     */
    public function hasFields()
    {
        return !!FieldTemplate::getListQuery($this->field_template_reference)->one();
    }

    /**
     * Return true if this block has conditions
     * @return bool
     * @throws CommonException
     */
    public function hasConditions()
    {
        return !!ConditionTemplate::getListQuery($this->condition_template_reference)->one();
    }

    /**
     * @inheritdoc
     * @throws CommonException
     */
    public function getFieldTemplateReference()
    {
        if (!$this->field_template_reference) {
            $this->field_template_reference = FieldTemplate::generateTemplateReference();
            $this->save(false);
        }

        return $this->field_template_reference;
    }

    /**
     * Unneeded method, but i don`t want to create more interfaces without serious reason
     * Some SOLID principles are crying)))
     * @throws CommonException
     */
    public function getFieldReference()
    {
        throw new CommonException('This method is unneeded and can`t be implemented there');
    }

    /**
     * @inheritdoc
     * @throws CommonException
     */
    public function getConditionTemplateReference()
    {
        if (!$this->condition_template_reference) {
            $this->condition_template_reference = ConditionTemplate::generateTemplateReference();
            $this->save(false);
        }

        return $this->condition_template_reference;
    }

    /**
     * @inheritdoc
     */
    public function getConditionReference()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getEntityQuery()
    {
        if (CommonModule::isUnderDev() || $this->editable)
            return Image::find()
                ->where([
                    'common_images_templates_id' => $this->id,
                    'image_reference'            => $this->currentImageReference
                ])
                ->indexBy('id')
                ->orderBy(['image_order' => SORT_ASC]);

        return new ActiveQuery(Image::className());
    }

    public function delete()
    {
        //parent::delete();
    }

    /**
     * @inheritdoc
     * @throws CommonException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    protected function deleteSequence()
    {
        foreach(ImagesNamesTranslatesDb::find()->where([
            'common_images_template_id' => $this->id,
        ])->all() as $imageName)
            if (!$imageName->delete()) return false;

        $fieldTemplateReferences = FieldTemplate::find()->where([
            'field_template_reference' => $this->getFieldTemplateReference()
        ])->all();

        if ($fieldTemplateReferences)
            foreach($fieldTemplateReferences as $fieldTemplate)
                $fieldTemplate->delete();

        $validators = ValidatorDb::find()->where([
            'validator_reference' => $this->validator_reference
        ])->all();

        if ($validators)
            foreach($validators as $validator)
                $validator->delete();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getOrderQuery()
    {
        return self::find()->where([
            'image_template_reference' => $this->image_template_reference,
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
        $this->scenario = self::SCENARIO_CHANGE_ORDER;
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
    protected static function getTemplateReferenceName()
    {
        return 'image_template_reference';
    }

    /**
     * @inheritdoc
     * @throws CommonException
     */
    public function getValidatorReference()
    {
        if (!$this->validator_reference) {
            $this->validator_reference = ValidatorBuilder::generateValidatorReference();
            $this->scenario = self::SCENARIO_UPDATE;
            $this->save(false);
        }

        return $this->validator_reference;
    }
}

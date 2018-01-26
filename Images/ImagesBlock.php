<?php

namespace Iliich246\YicmsCommon\Images;

use yii\db\ActiveQuery;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\AbstractEntityBlock;
use Iliich246\YicmsCommon\Languages\Language;
use Iliich246\YicmsCommon\Languages\LanguagesDb;
use Iliich246\YicmsCommon\Fields\FieldsHandler;
use Iliich246\YicmsCommon\Fields\FieldsInterface;
use Iliich246\YicmsCommon\Fields\FieldReferenceInterface;
use Iliich246\YicmsCommon\Validators\ValidatorBuilder;

/**
 * Class ImagesBlock
 *
 * @property string $image_template_reference
 * @property string $field_template_reference
 * @property string $validator_reference
 * @property integer $type
 * @property integer $language_type
 * @property integer $image_order
 * @property bool $visible
 * @property bool $editable
 * @property bool $max_images
 * @property bool $crop_type
 * @property bool $crop_height
 * @property bool $crop_width
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ImagesBlock extends AbstractEntityBlock implements
    FieldsInterface,
    FieldReferenceInterface
{
    /**
     * Images types
     */
    const TYPE_MULTIPLICITY = 0;
    const TYPE_ONE_IMAGE = 1;
    /**
     * Language types of images
     * Type define is image have translates or image has one instance independent of languages
     */
    const LANGUAGE_TYPE_TRANSLATABLE = 0;
    const LANGUAGE_TYPE_SINGLE = 1;

    /**
     * @var bool if true for this block will be created standard fields like filename
     */
    public $createStandardFields = true;
    /**
     * @var FieldsHandler instance of field handler object
     */
    private $fieldHandler;
    /**
     * @var ImagesNamesTranslatesDb[] buffer
     */
    private $imageNamesTranslates = [];
    /**
     * @var string fileReference for what files group must be fetched
     */
    private $currentImageReference;
    /**
     * @inheritdoc
     */
    protected static $buffer = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->visible = true;
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
            'max_images' => 'Maximum images in block'
        ]);
    }

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
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['type', 'language_type'], 'integer'],
            [['visible', 'editable'], 'boolean'],
            ['max_images', 'integer', 'min' => 0]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $prevScenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = array_merge($prevScenarios[self::SCENARIO_CREATE],
            ['type', 'language_type', 'visible', 'editable', 'max_images']);
        $scenarios[self::SCENARIO_UPDATE] = array_merge($prevScenarios[self::SCENARIO_UPDATE],
            ['type','language_type' ,'visible', 'editable', 'max_images']);

        return $scenarios;
    }

    /**
     * Return array of field types
     * @return array
     */
    public static function getTypes()
    {
        static $array = false;

        if ($array) return $array;

        $array = [
            self::TYPE_ONE_IMAGE => 'One image',
            self::TYPE_MULTIPLICITY => 'Multiple images',
        ];

        return $array;
    }

    /**
     * Return name of type of concrete field
     * @return mixed
     */
    public function getTypeName()
    {
        return self::getTypes()[$this->type];
    }

    /**
     * Return array of field language types
     * @return array
     */
    public static function getLanguageTypes()
    {
        static $array = false;

        if ($array) return $array;

        $array = [
            self::LANGUAGE_TYPE_SINGLE => 'Single type',
            self::LANGUAGE_TYPE_TRANSLATABLE => 'Translatable type',
        ];

        return $array;
    }

    /**
     * Return name of language type of concrete field
     * @return mixed
     */
    public function getLanguageTypeName()
    {
        return self::getLanguageTypes()[$this->language_type];
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
     * @return bool
     */
    public function isConstraints()
    {

    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function delete()
    {

    }

    /**
     * Renames parent method on concrete name
     * @return Image
     */
    public function getImage()
    {
        return $this->getEntity();
    }

    /**
     * Renames parent method on concrete name
     * @return Image[]
     */
    public function getImages()
    {
        return $this->getEntities();
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
                'common_language_id' => $language->id,
            ])->one();

            if (!$data) $this->imageNamesTranslates[$language->id] = null;
            else $this->imageNamesTranslates[$language->id] = $data;
        }

        return $this->imageNamesTranslates[$language->id];
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
        //return $this->field_reference;
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
                    'image_reference' => $this->currentImageReference
                ])
                ->indexBy('id')
                ->orderBy(['image_order' => SORT_ASC]);

        return new ActiveQuery(Image::className());
    }

    /**
     * @inheritdoc
     */
    public function getOrderQuery()
    {
        return self::find()->where([
            'image_template_reference' => $this->image_template_reference,
            'language_type' => $this->language_type,
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

<?php

namespace Iliich246\YicmsCommon\Images;

use Iliich246\YicmsCommon\Base\AbstractTranslateForm;

/**
 * Class ImageNamesTranslatesForm
 *
 * @property ImagesNamesTranslatesDb $currentTranslateDb
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ImageNamesTranslatesForm extends AbstractTranslateForm
{
    /** @var string name of image block template in current model language */
    public $name;
    /** @var string description of image block template on current model language */
    public $description;
    /** @var ImagesBlock associated with this model */
    private $imageBlockTemplate;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name'        => 'Image block name on language "' . $this->language->name . '"',
            'description' => 'Description of image block on language "' . $this->language->name . '"',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'string', 'max' => '50', 'tooLong' => 'Name of image must be less than 50 symbols'],
            ['description', 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getViewName()
    {
        return '@yicms-common/Views/translates/images_names_translate';
    }

    /**
     * Sets fileBlockTemplate associated with this object
     * @param ImagesBlock $imageBlockTemplate
     */
    public function setImagesBlockTemplate(ImagesBlock $imageBlockTemplate)
    {
        $this->imageBlockTemplate = $imageBlockTemplate;
    }

    /**
     * Saves record in data base
     * @return bool
     */
    public function save()
    {
        $this->getCurrentTranslateDb()->name                      = $this->name;
        $this->getCurrentTranslateDb()->description               = $this->description;
        $this->getCurrentTranslateDb()->common_language_id        = $this->language->id;
        $this->getCurrentTranslateDb()->common_images_template_id = $this->imageBlockTemplate->id;

        return $this->getCurrentTranslateDb()->save();
    }

    /**
     * @inheritdoc
     */
    protected function isCorrectConfigured()
    {
        if (!parent::isCorrectConfigured() || !$this->imageBlockTemplate) return false;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getCurrentTranslateDb()
    {
        if ($this->currentTranslateDb) return $this->currentTranslateDb;

        $this->currentTranslateDb = ImagesNamesTranslatesDb::find()
            ->where([
                'common_language_id'        => $this->language->id,
                'common_images_template_id' => $this->imageBlockTemplate->id,
            ])
            ->one();

        if (!$this->currentTranslateDb)
            $this->createTranslateDb();
        else {
            $this->name = $this->currentTranslateDb->name;
            $this->description = $this->currentTranslateDb->description;
        }

        return $this->currentTranslateDb;
    }

    /**
     * @inheritdoc
     */
    protected function createTranslateDb()
    {
        $this->currentTranslateDb                            = new ImagesNamesTranslatesDb();
        $this->currentTranslateDb->common_language_id        = $this->language->id;
        $this->currentTranslateDb->common_images_template_id = $this->imageBlockTemplate->id;

        return $this->currentTranslateDb->save();
    }
}

<?php

namespace Iliich246\YicmsCommon\Widgets;

use Iliich246\YicmsCommon\Base\CommonException;
use Yii;
use yii\base\Widget;
use Iliich246\YicmsCommon\Base\AbstractTranslateForm;

/**
 * Class SimpleTabsTranslatesWidget
 *
 * This widget must be used to render translate tabs for simple translate models with predefined
 * count of fields. This models used commonly in developer section.
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class SimpleTabsTranslatesWidget extends Widget
{
    /**
     * @var \yii\bootstrap\ActiveForm form, for render control elements in tabs
     */
    public $form;
    /**
     * @var AbstractTranslateForm[] array of translate models
     * This array represented as:
     * $this->translateModels = [
     *     <language-id1> => AbstractTranslateForm(for language-id1),
     *     <language-id2> => AbstractTranslateForm(for language-id2),
     *     ...
     *     <language-idN> => AbstractTranslateForm(for language-idN),
     * ]
     */
    public $translateModels;
    /**
     * @var mixed arbitrary data
     */
    public $data;
    /**
     * Prefix for modify bootstrap tabs if form has more than one same tab essences
     * @var bool|string
     */
    public $tabModification = false;
    /**
     * @var string path to translate view
     */
    private $translateView;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!$this->translateModels) return false;
        //TODO: makes correct handle of this error

        if (count($this->translateModels) == 1)
            return $this->render('@yicms-common/Widgets/simple-tabs-translate-widget/one-item',[
                'widget' => $this,
            ]);

        return $this->render('@yicms-common/Widgets/simple-tabs-translate-widget/tabs',[
            'widget' => $this,
        ]);
    }

    /**
     * Return view for render concrete AbstractTranslate
     * (This field get from concrete AbstractTranslate)
     * @return string
     * @throws CommonException
     */
    public function getTranslateView()
    {
        if ($this->translateView) return $this->translateView;

        /** @var AbstractTranslateForm $translate */
        $translate = current($this->translateModels);

        if (!($translate instanceof AbstractTranslateForm)) {
            Yii::error('Try to use in widget models, that are not descendants of AbstractTranslate', __METHOD__);
            throw new CommonException('Try to use in widget models, that are not descendants of AbstractTranslate');
        }

        return $this->translateView = $translate::getViewName();
    }
}

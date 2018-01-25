<?php

namespace Iliich246\YicmsCommon\Images;

use yii\bootstrap\Widget;

/**
 * Class ImagesRenderWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ImagesRenderWidget extends Widget
{
    /**
     * @var \yii\bootstrap\ActiveForm form, for render control elements in tabs
     */
    public $form;
    /**
     * @var ImagesGroup instance
     */
    public $imagesGroup;
    /**
     * @var ImagesBlock instance
     */
    public $imagesBlock;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->imagesBlock->language_type == ImagesBlock::LANGUAGE_TYPE_SINGLE)
            return $this->render('@yicms-common/Images/views/single_type', [
                'imagesGroup' => $this->imagesGroup,
                'imagesBlock' => $this->imagesBlock,
                'form'        => $this->form
            ]);
        else
            return $this->render('@yicms-common/Images/views/translate_able_type', [
                'imagesGroup' => $this->imagesGroup,
                'imagesBlock' => $this->imagesBlock,
                'form'        => $this->form
            ]);
    }
}

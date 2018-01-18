<?php

namespace Iliich246\YicmsCommon\Files;

use yii\bootstrap\Widget;

/**
 * Class FilesRenderWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FilesRenderWidget extends Widget
{
    /**
     * @var \yii\bootstrap\ActiveForm form, for render control elements in tabs
     */
    public $form;
    /**
     * @var FilesGroup instance
     */
    public $filesGroup;
    /**
     * @var FilesBlock instance
     */
    public $filesBlock;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->filesBlock->language_type == FilesBlock::LANGUAGE_TYPE_SINGLE)
            return $this->render('@yicms-common/Files/views/single_type', [
                'filesGroup' => $this->filesGroup,
                'form' => $this->form
            ]);
        else
            return $this->render('@yicms-common/Files/views/translate_able_type', [
                'filesGroup' => $this->filesGroup,
                'form' => $this->form
            ]);
    }

}

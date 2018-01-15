<?php

namespace Iliich246\YicmsCommon\Files;

use yii\bootstrap\Widget;
use Iliich246\YicmsCommon\CommonModule;

/**
 * Class FilesListModalWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FilesListModalWidget extends Widget
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render(CommonModule::getInstance()->yicmsLocation . '/Common/Views/files/files-list-modal', [
            'widget' => $this
        ]);
    }
}

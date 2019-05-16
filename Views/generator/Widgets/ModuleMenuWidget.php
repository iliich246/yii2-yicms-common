<?php

namespace app\yicms\Common\Widgets;

use Yii;
use Iliich246\YicmsCommon\CommonModule;
use Iliich246\YicmsCommon\Base\AbstractModuleMenuWidget;
use Iliich246\YicmsCommon\FreeEssences\FreeEssences;

/**
 * Class ModuleMenuWidget
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class ModuleMenuWidget extends AbstractModuleMenuWidget
{
    /**
     * @inheritdoc
     */
    public static function getModuleName()
    {
        return strtolower(CommonModule::getModuleName());
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->route = Yii::$app->controller->action->getUniqueId();

        $freeEssencesQuery = FreeEssences::find()->orderBy(['free_essences_order' => SORT_ASC]);

        if (!CommonModule::isUnderDev())
            $freeEssencesQuery->where([
                'editable' => true,
            ]);

        $freeEssences = $freeEssencesQuery->all();

        return $this->render('module_menu', [
            'widget'       => $this,
            'freeEssences' => $freeEssences
        ]);
    }

    /**
     * Return true, if for this free essence element of menu must be active
     * @param FreeEssences $freeEssence
     * @return bool
     */
    public function isActive(FreeEssences $freeEssence)
    {
        if ($this->route == 'common/admin/edit-free-essence' && Yii::$app->request->get('id') == $freeEssence->id) return true;
        return false;
    }
}

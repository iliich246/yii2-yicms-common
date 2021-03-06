<?php

namespace Iliich246\YicmsCommon\Files;

use Iliich246\YicmsCommon\Conditions\ConditionTemplate;
use yii\base\Component;
use Iliich246\YicmsCommon\Base\CommonException;
use Iliich246\YicmsCommon\Fields\FieldTemplate;
use Iliich246\YicmsCommon\Annotations\AnnotatorStringInterface;

/**
 * Class FileBlockAnnotatorString
 *
 * This class needed only for generation annotation string.
 *
 * @author iliich246 <iliich246@gmail.com>
 */
class FileBlockAnnotatorString extends Component implements AnnotatorStringInterface
{
    /**
     * @inheritdoc
     * @param FilesBlock $searchData
     * @return array|mixed
     * @throws \ReflectionException
     * @throws CommonException
     */
    public static function getAnnotationsStringArray($searchData)
    {
        /** @var FieldTemplate[] $templates */
        $templates = FieldTemplate::find()->where([
            'field_template_reference' => $searchData->getFieldTemplateReference()
        ])->orderBy([
            'field_order' => SORT_ASC
        ])->all();

        $result = [];

        if ($templates) {
            $result[] = ' * ' . PHP_EOL;
            $result[] = ' * FIELDS' . PHP_EOL;
        }

        FieldTemplate::setParentFileAnnotator($searchData);

        foreach($templates as $template) {
            $result[] = ' * @property string $' . $template->program_name . ' ' . PHP_EOL;
            $result[] = ' * @property string $field_' . $template->program_name . ' ' . PHP_EOL;

            $template->annotate();
        }

        /** @var ConditionTemplate $conditionTemplates */
        $conditionTemplates = ConditionTemplate::find()->where([
            'condition_template_reference' => $searchData->getConditionTemplateReference()
        ])->orderBy([
            'condition_order' => SORT_ASC
        ])->all();

        if ($conditionTemplates) {
            $result[] = ' * ' . PHP_EOL;
            $result[] = ' * CONDITIONS' . PHP_EOL;
        }

        ConditionTemplate::setParentFileAnnotator($searchData);

        foreach ($conditionTemplates as $template) {
            $result[] = ' * @property ' . '\\' .
                $template->getAnnotationFileNamespace() . '\\' .
                $template->getAnnotationFileName() .
                ' $' . $template->program_name . ' ' . PHP_EOL;
            $result[] = ' * @property ' . '\\' .
                $template->getAnnotationFileNamespace() . '\\' .
                $template->getAnnotationFileName() .
                ' $condition_' . $template->program_name . ' ' . PHP_EOL;
            $template->annotate();
        }

        return $result;
    }
}

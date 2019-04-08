<?php

/** @var $this yii\web\View */
/** @var $annotator \Iliich246\YicmsCommon\Annotations\Annotator */
/** @var $imageBlockInstance \Iliich246\YicmsCommon\Images\ImagesBlock */

$imageBlockInstance = $annotator->getAnnotatorFileObject();
echo "<?php\n";
?>

namespace <?= $annotator->getNamespace() ?>;

use Yii;
use <?= $annotator->getExtendsUseClass() ?>;

/**
* Class <?= $annotator->getClassName() ?>

*
* This class was generated automatically
*
* |||-> This part of annotation will be change automatically. Do not change it.
*
* |||<- End of block of auto annotation
*
* @method <?= $imageBlockInstance->getClassNameForImageClass() ?> getImage();
* @method <?= $imageBlockInstance->getClassNameForImageClass() ?>[] getImages();
*
* @author iliich246 <iliich246@gmail.com>
*/
class <?= $annotator->getClassName() ?> extends <?= $annotator->getExtendsClassName() ?>

{
    /** @inheritdoc */
    protected static $buffer = [];

}


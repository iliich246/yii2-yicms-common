<?php

/** @var $this \yii\web\View */
/** @var $translateModel \Iliich246\YicmsCommon\Conditions\ConditionValueNamesForm */

?>

<?= $form->field($translateModel, "[$translateModel->key]valueName")->textInput() ?>

<?= $form->field($translateModel, "[$translateModel->key]valueDescription")->textarea() ?>

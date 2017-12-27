<?php

use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $freeEssences \Iliich246\YicmsCommon\FreeEssences\FreeEssences[] */

?>

<?php Pjax::begin([
    'options' => [
        'id' => 'update-free-essences-list-container'
    ],
    'linkSelector' => false,
]) ?>
<div class="list-block">
    <?php foreach($freeEssences as $freeEssence): ?>
        <div class="row list-items">
            <div class="col-xs-10 list-title">
                <a href="<?= Url::toRoute(['update-free-essence', 'id' => $freeEssence->id]) ?>">
                    <p>
                        <?= $freeEssence->program_name ?>
                    </p>
                </a>
            </div>
            <div class="col-xs-2 list-controls">
                <?php if ($freeEssence->visible): ?>
                    <span class="glyphicon glyphicon-eye-open"></span>
                <?php else: ?>
                    <span class="glyphicon glyphicon-eye-close"></span>
                <?php endif; ?>
                <?php if ($freeEssence->editable): ?>
                    <span class="glyphicon glyphicon-pencil"></span>
                <?php endif; ?>
                <?php if ($freeEssence->canUpOrder()): ?>
                    <span class="glyphicon glyphicon-arrow-up"
                          data-free-essence-id="<?= $freeEssence->id ?>"></span>
                <?php endif; ?>
                <?php if ($freeEssence->canDownOrder()): ?>
                    <span class="glyphicon glyphicon-arrow-down"
                          data-free-essence-id="<?= $freeEssence->id ?>"></span>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php Pjax::end() ?>

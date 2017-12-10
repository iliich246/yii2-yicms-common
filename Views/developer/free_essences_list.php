<?php

use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $freeEssences \Iliich246\YicmsCommon\FreeEssences\FreeEssences[] */
?>

<div class="col-sm-9 content">
    <div class="row content-block content-header">
        <h1>List of free essences</h1>
    </div>
    <div class="row content-block">
        <div class="col-xs-12">
            <div class="row control-buttons">
                <div class="col-xs-12">
                    <a href="<?= Url::toRoute(['create-free-essence']) ?>" class="btn btn-primary">
                        Create new free essence
                    </a>
                </div>
            </div>

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
                            <?php if ($freeEssence->editable): ?>
                                <span class="glyphicon glyphicon-eye-open"></span>
                            <?php else: ?>
                                <span class="glyphicon glyphicon-eye-close"></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

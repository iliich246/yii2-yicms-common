<?php

use yii\helpers\Url;
use Iliich246\YicmsCommon\Assets\FreeEssencesDevAsset;

/* @var $this \yii\web\View */
/* @var $freeEssences \Iliich246\YicmsCommon\FreeEssences\FreeEssences[] */

FreeEssencesDevAsset::register($this);
?>

<div class="col-sm-9 content">
    <div class="row content-block content-header">
        <h1>List of free essences</h1>
    </div>
    <div class="row content-block">
        <div class="col-xs-12">
            <div class="row control-buttons">
                <div class="col-xs-12">
                    <a href="<?= Url::toRoute(['create-free-essence']) ?>"
                       class="btn btn-primary create-essence-button"
                       data-home-url="<?= Url::base() ?>" >
                        Create new free essence
                    </a>
                </div>
            </div>

            <?= $this->render('/pjax/update-free-essence-list-container', [
                'freeEssences' => $freeEssences
            ]) ?>

        </div>
    </div>
</div>

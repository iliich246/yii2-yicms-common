<?php //template

/* @var $this \yii\web\View */
/* @var $widget \Iliich246\YicmsCommon\Widgets\TopDevMenuWidget */

?>

<div class="menu-items-block">
    <div class="logo-padding"></div>
    <div class="row user-block">
        <div class="col-xs-12">
            <h3>You are entered as:</h3>
            <span class="glyphicon glyphicon-user"></span>
            <strong>ADMIN</strong>
        </div>
    </div>
    <div class="scroll-block">
        <hr>
        <?= $widget->renderMenuWidgets() ?>
    </div>
    <div class="row button-block">
        <div class="col-xs-12">
            <a href="#">Exit</a>
        </div>
    </div>
</div>

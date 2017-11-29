<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use Iliich246\YicmsCommon\Assets\DeveloperAsset;

DeveloperAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link href="/web/favicon.ico" rel="shortcut icon" type="image/x-icon" />
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<nav id="mobile-menu">
    <header>
        <!-- There will be inserted template -->
    </header>
</nav>
<header>
    <nav id="navbar" class="navbar navbar-default">
        <div class="container">
            <div class="row">
                <div class="col-xs-3 navbar-logo">

                </div>
                <div class="col-xs-4 nav-items hidden-xs">
                    <h1>Developer panel</h1>
                </div>
            </div>
        </div>
    </nav>
</header>
<main id="panel">
    <div class="container" id="content-container">
        <div class="row">
            <div class="col-sm-3 menu-block hidden-xs">
                <div class="menu-content">
                    <div class="menu-logo">
                        <a href="/">
                            LOGO
                        </a>
                    </div>
                    <?= \Iliich246\YicmsCommon\Widgets\TopDevMenuWidget::widget() ?>
                    <!-- There will be inserted template -->
                </div>
            </div>
            <?= $content ?>
        </div>
    </div>
</main>
<?= \Iliich246\YicmsCommon\Widgets\ReloadAlertWidget::widget() ?>
<?php $this->endBody() ?>
<script type="text/template" id="menu-template">
    <?= \Iliich246\YicmsCommon\Widgets\TopDevMenuWidget::widget() ?>
</script>
</body>
</html>
<?php $this->endPage() ?>

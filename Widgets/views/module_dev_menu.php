<?php

use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $widget \Iliich246\YicmsCommon\Widgets\ModuleDevMenuWidget */

?>

<div class="row link-block">
    <div class="col-xs-12">
        <h2>Common module</h2>
        <a <?php if ($widget->route == 'common/dev/languages-list'): ?> class="active" <?php endif; ?>
            href="<?= Url::toRoute('/common/dev/languages-list') ?>">
            List of languages
        </a>
        <a <?php if (
                        ($widget->route == 'common/dev/create-language')
                            ||
                        ($widget->route == 'common/dev/update-language')
                    ):?> class="active" <?php endif; ?>
            href="<?= Url::toRoute('/common/dev/create-language') ?>">
            Create/update language
        </a>
        <a <?php if ($widget->route == 'common/dev/change-dev-hash'): ?> class="active" <?php endif; ?>
            href="<?= Url::toRoute('/common/dev/change-dev-hash') ?>">
            Change dev hash
        </a>
        <a <?php if ($widget->route == 'common/dev/change-admin-hash'): ?> class="active" <?php endif; ?>
            href="<?= Url::toRoute('/common/dev/change-admin-hash') ?>">
            Change admin hash
        </a>
        <a <?php if ($widget->route == 'common/dev/free-essences-list'): ?> class="active" <?php endif; ?>
            href="<?= Url::toRoute('/common/dev/free-essences-list') ?>">
            Free essences list
        </a>
        <a <?php if (
            ($widget->route == 'common/dev/create-free-essence')
            ||
            ($widget->route == 'common/dev/update-free-essence')
        ):?> class="active" <?php endif; ?>
            href="<?= Url::toRoute('/common/dev/create-free-essence') ?>">
            Create/update free essence
        </a>
        <a <?php if ($widget->route == 'common/dev/maintenance'): ?> class="active" <?php endif; ?>
            href="<?= Url::toRoute('/common/dev/maintenance') ?>">
            Maintenance
        </a>
    </div>
</div>
<hr>

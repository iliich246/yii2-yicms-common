;(function() {
    var createEssenceButton = $('.create-essence-button');
    var homeUrl = $(createEssenceButton).data('homeUrl');
    var freeEssenceUpUrl = homeUrl + '/common/dev/free-essence-up-order';
    var freeEssenceDownUrl = homeUrl + '/common/dev/free-essence-down-order';

    var pjaxContainer = $('#update-free-essences-list-container');

    $(document).on('click', '.glyphicon-arrow-up', function() {
        $.pjax({
            url: freeEssenceUpUrl + '?freeEssenceId=' + $(this).data('freeEssenceId'),
            container: '#update-free-essences-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.glyphicon-arrow-down', function() {
        $.pjax({
            url: freeEssenceDownUrl + '?freeEssenceId=' + $(this).data('freeEssenceId'),
            container: '#update-free-essences-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(pjaxContainer).on('pjax:error', function(xhr, textStatus) {
        bootbox.alert({
            size: 'large',
            title: "There are some error on ajax request!",
            message: textStatus.responseText,
            className: 'bootbox-error'
        });
    });
})();

;(function() {
    var addFile = $('.add-file-block');

    var homeUrl = $(addFile).data('homeUrl');
    var emptyModalUrl = homeUrl + '/common/dev-files/empty-modal';
    var loadModalUrl = homeUrl + '/common/dev-files/load-modal';
    var updateFileListUrl = homeUrl + '/common/dev-files/update-files-list-container';
    var fileTemplateUpUrl = homeUrl + '/common/dev-files/file-template-up-order';
    var filedTemplateDownUrl = homeUrl + '/common/dev-files/file-template-down-order';

    var fileTemplateReference = $(addFile).data('fileTemplateReference');
    var pjaxContainerName = '#' + $(addFile).data('pjaxContainerName');
    var pjaxFilesModalName = '#' + $(addFile).data('filesModalName');
    var imageLoaderScr = $(addFile).data('loaderImageSrc');

    $(pjaxContainerName).on('pjax:send', function() {
        $(pjaxFilesModalName)
            .find('.modal-content')
            .empty()
            .append('<img src="' + imageLoaderScr + '" style="text-align:center">');
    });

    $(pjaxContainerName).on('pjax:success', function(event) {

        var isValidatorResponse = !!($('.validator-response').length);

        if (isValidatorResponse) return loadModal($(addFile).data('currentSelectedFileTemplate'));

        if (!$(event.target).find('form').is('[data-yicms-saved]')) return false;

        $.pjax({
            url: updateFileListUrl + '?fileTemplateReference=' + fileTemplateReference,
            container: '#update-files-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });

        if (!isValidatorResponse)
            $(pjaxFilesModalName).modal('hide');
    });

    $(document).on('click', '.file-item p', function(event) {
        var fileTemplate = $(this).data('file-template-id');

        $(addFile).data('currentSelectedFileTemplate',fileTemplate);

        loadModal(fileTemplate);
    });

    $(addFile).on('click', function() {
        $.pjax({
            url: emptyModalUrl + '?fileTemplateReference=' + fileTemplateReference ,
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.file-arrow-up', function() {
        $.pjax({
            url: fileTemplateUpUrl + '?fileTemplateId=' + $(this).data('fileTemplateId'),
            container: '#update-files-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.file-arrow-down', function() {
        $.pjax({
            url: filedTemplateDownUrl + '?fileTemplateId=' + $(this).data('fileTemplateId'),
            container: '#update-files-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    function loadModal(fileTemplate) {
        $.pjax({
            url: loadModalUrl + '?fileTemplateId=' + fileTemplate,
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });

        $(pjaxFilesModalName).modal('show');
    }
})();

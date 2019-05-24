;(function() {
    var addFile = $('.add-file-block');

    var homeUrl = $(addFile).data('homeUrl');

    var emptyModalUrl           = homeUrl + '/common/dev-files/empty-modal';
    var loadModalUrl            = homeUrl + '/common/dev-files/load-modal';
    var updateFileListUrl       = homeUrl + '/common/dev-files/update-files-list-container';
    var fileTemplateUpUrl       = homeUrl + '/common/dev-files/file-template-up-order';
    var filedTemplateDownUrl    = homeUrl + '/common/dev-files/file-template-down-order';
    var showFieldsListModal     = homeUrl + '/common/dev-fields/update-fields-list-container-dependent';
    var showConditionsListModal = homeUrl + '/common/dev-conditions/update-conditions-list-container-dependent';

    var fileTemplateReference = $(addFile).data('fileTemplateReference');
    var pjaxContainerName     = '#' + $(addFile).data('pjaxContainerName');
    var pjaxFilesModalName    = '#' + $(addFile).data('filesModalName');
    var imageLoaderScr        = $(addFile).data('loaderImageSrc');

    var redirectToUpdateNeedSecondPjaxRequest = false;

    $(pjaxContainerName).on('pjax:send', function() {
        $(pjaxFilesModalName)
            .find('.modal-content')
            .empty()
            .append('<img src="' + imageLoaderScr + '" style="text-align:center">');
    });

    $(pjaxContainerName).on('pjax:success', function(event) {

        var fileForm = $('#create-update-files');

        if ($(fileForm).data('saveAndExit')) {
            $(pjaxFilesModalName).modal('hide');

            $.pjax({
                url: updateFileListUrl + '?fileTemplateReference=' + fileTemplateReference,
                container: '#update-files-list-container',
                scrollTo: false,
                push: false,
                type: "POST",
                timeout: 2500
            });

            return;
        }

        var redirectToUpdate           = $(fileForm).data('redirectToUpdateFile');
        var fieldTemplateIdForRedirect = $(fileForm).data('fileBlockIdRedirect');

        if (redirectToUpdate) {
            $.pjax({
                url: updateFileListUrl + '?fileTemplateReference=' + fileTemplateReference,
                container: '#update-files-list-container',
                scrollTo: false,
                push: false,
                type: "POST",
                timeout: 2500
            });

            redirectToUpdateNeedSecondPjaxRequest = fieldTemplateIdForRedirect;

            return;
        }


        var isValidatorResponse = !!($('.validator-response').length);

        if (isValidatorResponse) return goBackValidator();

        if (!$(event.target).find('form').is('[data-yicms-saved]')) return false;

        $.pjax({
            url: updateFileListUrl + '?fileTemplateReference=' + fileTemplateReference,
            container: '#update-files-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $('#update-files-list-container').on('pjax:success', function(event) {
        if (redirectToUpdateNeedSecondPjaxRequest) {
            loadModal(redirectToUpdateNeedSecondPjaxRequest);
            redirectToUpdateNeedSecondPjaxRequest = false;
        }
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

    $(document).on('click', '.view-files-block-fields', function() {

        var container = $('#files-pjax-container');
        $(container).data('returnUrl', $(this).data('returnUrl'));
        $(container).data('annotateUrl', $(this).data('annotateUrl'));

        $.pjax({
            url: showFieldsListModal + '?fieldTemplateReference=' + $(this).data('fieldTemplateId')
                 + '&pjaxName=' + pjaxContainerName.substr(1)
                 + '&modalName=' + pjaxFilesModalName.substr(1),
            container: '#files-pjax-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.view-files-block-conditions', function() {
        var container = $('#files-pjax-container');

        $(container).data('returnUrl', $(this).data('returnUrl'));
        $(container).data('annotateUrl', $(this).data('annotateUrl'));

        $.pjax({
            url: showConditionsListModal + '?conditionTemplateReference=' + $(this).data('conditionTemplateId')
            + '&pjaxName=' + pjaxContainerName.substr(1)
            + '&modalName=' + pjaxFilesModalName.substr(1),
            container: '#files-pjax-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    function goBackValidator() {
        var returnUrl = $(pjaxContainerName).data('returnUrlValidators');

        $.pjax({
            url: returnUrl,
            container: '#files-pjax-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500,
        });
    }

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

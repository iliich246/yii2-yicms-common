;(function() {
    var addField = $('.add-field');

    var homeUrl = $(addField).data('homeUrl');

    var emptyModalUrl        = homeUrl + '/common/dev-fields/empty-modal';
    var loadModalUrl         = homeUrl + '/common/dev-fields/load-modal';
    var updateFieldsListUrl  = homeUrl + '/common/dev-fields/update-fields-list-container';
    var fieldTemplateUpUrl   = homeUrl + '/common/dev-fields/field-template-up-order';
    var fieldTemplateDownUrl = homeUrl + '/common/dev-fields/field-template-down-order';

    var fieldTemplateReference = $(addField).data('fieldTemplateReference');
    var pjaxContainerName      = '#' + $(addField).data('pjaxContainerName');
    var pjaxFieldsModalName    = '#' + $(addField).data('fieldsModalName');
    var imageLoaderScr         = $(addField).data('loaderImageSrc');

    var redirectToUpdateNeedSecondPjaxRequest = false;

    $(pjaxContainerName).on('pjax:send', function() {
        $(pjaxFieldsModalName)
            .find('.modal-content')
            .empty()
            .append('<img src="' + imageLoaderScr + '" style="text-align:center">');
    });

    $(pjaxContainerName).on('pjax:success', function(event) {

        var fieldTemplateHidden = $('#field-template-id-hidden');

        if ($(fieldTemplateHidden).val())
            $(addField).data('currentSelectedFieldTemplate', $(fieldTemplateHidden).val());

        var fieldForm = $('#create-update-fields');

        if ($(fieldForm).data('saveAndExit')) {
            $(pjaxFieldsModalName).modal('hide');

            $.pjax({
                url: updateFieldsListUrl + '?fieldTemplateReference=' + fieldTemplateReference,
                container: '#update-fields-list-container',
                scrollTo: false,
                push: false,
                type: "POST",
                timeout: 2500
            });

            return;
        }

        var redirectToUpdate           = $(fieldForm).data('redirectToUpdateField');
        var fieldTemplateIdForRedirect = $(fieldForm).data('fieldTemplateIdRedirect');

        if (redirectToUpdate) {
            $.pjax({
                url: updateFieldsListUrl + '?fieldTemplateReference=' + fieldTemplateReference,
                container: '#update-fields-list-container',
                scrollTo: false,
                push: false,
                type: "POST",
                timeout: 2500
            });

            redirectToUpdateNeedSecondPjaxRequest = fieldTemplateIdForRedirect;

            return;
        }

        var isValidatorResponse = !!($('.validator-response').length);

        if (isValidatorResponse) return loadModal($(addField).data('currentSelectedFieldTemplate'));

        if (!$(event.target).find('form').is('[data-yicms-saved]')) return false;

        $.pjax({
            url: updateFieldsListUrl + '?fieldTemplateReference=' + fieldTemplateReference,
            container: '#update-fields-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $('#update-fields-list-container').on('pjax:success', function(event) {
        if (redirectToUpdateNeedSecondPjaxRequest) {
            loadModal(redirectToUpdateNeedSecondPjaxRequest);
            redirectToUpdateNeedSecondPjaxRequest = false;
        }
    });


    $(document).on('click', '.field-item p', function(event) {
        var fieldTemplate = $(this).data('field-template-id');

        $(addField).data('currentSelectedFieldTemplate',fieldTemplate);

        loadModal(fieldTemplate);
    });

    $(addField).on('click', function() {
        $.pjax({
            url: emptyModalUrl + '?fieldTemplateReference=' + fieldTemplateReference ,
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.field-arrow-up', function() {
        $.pjax({
            url: fieldTemplateUpUrl + '?fieldTemplateId=' + $(this).data('fieldTemplateId'),
            container: '#update-fields-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.field-arrow-down', function() {
        $.pjax({
            url: fieldTemplateDownUrl + '?fieldTemplateId=' + $(this).data('fieldTemplateId'),
            container: '#update-fields-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    function loadModal($fieldTemplate) {
        $.pjax({
            url: loadModalUrl + '?fieldTemplateId=' + $fieldTemplate,
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });

        $(pjaxFieldsModalName).modal('show');
    }
})();

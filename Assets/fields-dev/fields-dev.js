;(function() {
    var addField = $('.add-field');

    var homeUrl = $(addField).data('homeUrl');
    var emptyModalUrl = homeUrl + '/common/dev-fields/empty-modal';
    var loadModalUrl = homeUrl + '/common/dev-fields/load-modal';
    var updateFieldsListUrl = homeUrl + '/common/dev-fields/update-fields-list-container';
    var fieldTemplateUpUrl = homeUrl + '/common/dev-fields/field-template-up-order';
    var fieldTemplateDownUrl = homeUrl + '/common/dev-fields/field-template-down-order';

    var fieldTemplateReference = $(addField).data('fieldTemplateReference');
    var pjaxContainerName = '#' + $(addField).data('pjaxContainerName');
    var pjaxFieldsModalName = '#' + $(addField).data('fieldsModalName');
    var imageLoaderScr = $(addField).data('loaderImageSrc');

    $(pjaxContainerName).on('pjax:send', function() {
        $(pjaxFieldsModalName)
            .find('.modal-content')
            .empty()
            .append('<img src="' + imageLoaderScr + '" style="text-align:center">');
    });

    $(pjaxContainerName).on('pjax:success', function(event) {

        if (!$(event.target).find('form').is('[data-yicms-saved]')) return false;

        $.pjax({
            url: updateFieldsListUrl + '?fieldTemplateReference=' + fieldTemplateReference,
            container: '#update-fields-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });

        $(pjaxFieldsModalName).modal('hide');
    });

    $(document).on('click', '.field-item p', function(event) {

        var templateData = $(this).data('field-template-id');

        $.pjax({
            url: loadModalUrl + '?fieldTemplateId=' + templateData,
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });

        $(pjaxFieldsModalName).modal('show');
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

    $(document).on('click', '.glyphicon-arrow-up', function() {
        $.pjax({
            url: fieldTemplateUpUrl + '?fieldTemplateId=' + $(this).data('fieldTemplateId'),
            container: '#update-fields-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.glyphicon-arrow-down', function() {
        $.pjax({
            url: fieldTemplateDownUrl + '?fieldTemplateId=' + $(this).data('fieldTemplateId'),
            container: '#update-fields-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });
})();

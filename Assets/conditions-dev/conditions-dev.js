;(function() {
    var addCondition = $('.add-condition-template');

    var homeUrl = $(addCondition).data('homeUrl');
    var emptyModalUrl = homeUrl + '/common/dev-conditions/empty-modal';
    var loadModalUrl = homeUrl + '/common/dev-conditions/load-modal';
    var updateConditionListUrl = homeUrl + '/common/dev-conditions/update-conditions-list-container';
    var conditionTemplateUpUrl = homeUrl + '/common/dev-conditions/condition-template-up-order';
    var conditionTemplateDownUrl = homeUrl + '/common/dev-conditions/condition-template-down-order';

    var conditionTemplateReference = $(addCondition).data('conditionTemplateReference');
    var pjaxContainerName = '#' + $(addCondition).data('pjaxContainerName');
    var pjaxConditionsModalName = '#' + $(addCondition).data('conditionModalName');
    var imageLoaderScr = $(addCondition).data('loaderImageSrc');

    $(pjaxContainerName).on('pjax:send', function() {
        $(pjaxConditionsModalName)
            .find('.modal-content')
            .empty()
            .append('<img src="' + imageLoaderScr + '" style="text-align:center">');
    });

    $(pjaxContainerName).on('pjax:success', function(event) {

        var isValidatorResponse = !!($('.validator-response').length);

        if (isValidatorResponse) return loadModal($(addCondition).data('currentSelectedConditionTemplate'));

        if (!$(event.target).find('form').is('[data-yicms-saved]')) return false;

        $.pjax({
            url: updateConditionListUrl + '?conditionTemplateReference=' + conditionTemplateReference,
            container: '#update-conditions-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 12500
        });

        if (!isValidatorResponse)
            $(pjaxContainerName).modal('hide');
    });

    $(document).on('click', '.condition-item p', function(event) {
        var conditionTemplate = $(this).data('conditionTemplateId');

        $(addCondition).data('currentSelectedConditionTemplate',conditionTemplate);

        loadModal(conditionTemplate);
    });

    $(addCondition).on('click', function() {
        $.pjax({
            url: emptyModalUrl + '?conditionTemplateReference=' + conditionTemplateReference ,
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.condition-arrow-up', function() {
        $.pjax({
            url: conditionTemplateUpUrl + '?conditionTemplateId=' + $(this).data('conditionTemplateId'),
            container: '#update-conditions-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.condition-arrow-down', function() {
        $.pjax({
            url: conditionTemplateDownUrl + '?conditionTemplateId=' + $(this).data('conditionTemplateId'),
            container: '#update-conditions-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.condition-data-list', function() {
        alert(1);

        //$('#images-pjax-container').data('returnUrl', $(this).data('returnUrl'));
        //
        //$.pjax({
        //    url: showFieldsListModal + '?fieldTemplateReference=' + $(this).data('fieldTemplateId')
        //    + '&pjaxName='  + pjaxContainerName.substr(1)
        //    + '&modalName=' + pjaxImagesModalName.substr(1),
        //    container: '#images-pjax-container',
        //    scrollTo: false,
        //    push: false,
        //    type: "POST",
        //    timeout: 2500
        //});
    });

    function loadModal(conditionTemplate) {
        $.pjax({
            url: loadModalUrl + '?conditionTemplateId=' + conditionTemplate,
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });

        $(pjaxConditionsModalName).modal('show');
    }
})();

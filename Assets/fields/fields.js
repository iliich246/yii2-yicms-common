;(function(){
    var editFieldsForm = $('#edit-fields-form');
    var baseAction = $(editFieldsForm).attr('action');

    var fieldPjaxContainer = $('#edit-fields-container');
    var homeUrl = $(fieldPjaxContainer).data('homeUrl');
    var fieldTemplateReference = $(fieldPjaxContainer).data('fieldTemplateReference');
    var changeVisibleUrl = homeUrl + '/common/admin-fields/change-field-visible';
    var changeEditableUrl = homeUrl + '/common/dev-fields/change-field-editable';

    $(document).on('click', '.field-visible-link', function(e) {

        $.pjax({
            url: changeVisibleUrl + '?fieldTemplateReference=' + fieldTemplateReference
            + '&fieldId=' + $(this).data('fieldId'),
            container: '#edit-fields-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.field-editable-link', function(e) {

        $.pjax({
            url: changeEditableUrl + '?fieldTemplateReference=' + fieldTemplateReference
            + '&fieldId=' + $(this).data('fieldId'),
            container: '#edit-fields-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(fieldPjaxContainer).on('pjax:success', function(event) {
        $('#edit-fields-form').attr('action', baseAction);
    });
})();

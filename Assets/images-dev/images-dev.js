;(function() {
    var addImage = $('.add-image-block');

    var homeUrl = $(addImage).data('homeUrl');

    var emptyModalUrl        = homeUrl + '/common/dev-images/empty-modal';
    var loadModalUrl         = homeUrl + '/common/dev-images/load-modal';
    var updateImageListUrl   = homeUrl + '/common/dev-images/update-images-list-container';
    var imageTemplateUpUrl   = homeUrl + '/common/dev-images/image-template-up-order';
    var imageTemplateDownUrl = homeUrl + '/common/dev-images/image-template-down-order';
    var showThumbnailsList   = homeUrl + '/common/dev-images/show-thumbnails-list';

    var imageTemplateReference = $(addImage).data('imageTemplateReference');
    var pjaxContainerName      = '#' + $(addImage).data('pjaxContainerName');
    var pjaxImagesModalName    = '#' + $(addImage).data('imagesModalName');
    var imageLoaderScr         = $(addImage).data('loaderImageSrc');

    $(pjaxContainerName).on('pjax:send', function() {
        $(pjaxImagesModalName)
            .find('.modal-content')
            .empty()
            .append('<img src="' + imageLoaderScr + '" style="text-align:center">');
    });

    $(pjaxContainerName).on('pjax:success', function(event) {

        var isValidatorResponse = !!($('.validator-response').length);

        if (isValidatorResponse) return loadModal($(addImage).data('currentSelectedImageTemplate'));

        if (!$(event.target).find('form').is('[data-yicms-saved]')) return false;

        $.pjax({
            url: updateImageListUrl + '?imageTemplateReference=' + imageTemplateReference,
            container: '#update-images-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });

        if (!isValidatorResponse)
            $(pjaxImagesModalName).modal('hide');
    });

    $(document).on('click', '.image-item p', function(event) {
        var imageTemplate = $(this).data('image-template-id');

        $(addImage).data('currentSelectedImageTemplate',imageTemplate);

        loadModal(imageTemplate);
    });

    $(addImage).on('click', function() {
        $.pjax({
            url: emptyModalUrl + '?imageTemplateReference=' + imageTemplateReference ,
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.image-arrow-up', function() {
        $.pjax({
            url: imageTemplateUpUrl + '?imageTemplateId=' + $(this).data('imageTemplateId'),
            container: '#update-images-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.image-arrow-down', function() {
        $.pjax({
            url: imageTemplateDownUrl + '?imageTemplateId=' + $(this).data('imageTemplateId'),
            container: '#update-images-list-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });

    $(document).on('click', '.config-thumbnails-button', function() {

        $.pjax({
            url: showThumbnailsList + '?imageTemplateId=' + $(this).data('imageTemplateId'),
            container: '#images-pjax-container',
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });
    });



    function loadModal(imageTemplate) {
        $.pjax({
            url: loadModalUrl + '?imageTemplateId=' + imageTemplate,
            container: pjaxContainerName,
            scrollTo: false,
            push: false,
            type: "POST",
            timeout: 2500
        });

        $(pjaxImagesModalName).modal('show');
    }
})();

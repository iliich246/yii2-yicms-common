$(function() {
    $(document).on('click', '.btn-boot-box', function(e) {
        var title = $(this).data('title');
        var message = $(this).data('message');
        var url = $(this).data('url');
        var okLabel = $(this).data('ok-label');
        var okCancelLabel = $(this).data('cancel-label');
        var viaPjax = $(this).is('[data-via-pjax]');
        var pjaxContainer = '#' + $(this).data('pjax-container');

        e.preventDefault();
        
        if (url) {
            bootbox.dialog({
                backdrop: 'static',
                title: title,
                message: message,
                buttons: {
                    success: {
                        label: okLabel,
                        className: 'btn-danger',
                        callback: function() {

                            if (!viaPjax)
                                return $(location).attr('href',url);

                            $.pjax({
                                url: url,
                                container: pjaxContainer,
                                scrollTo: false,
                                push: false,
                                type: "POST",
                                timeout: 2500
                            });

                            $('.bootbox').modal('hide');
                        }
                    },
                    cancel: {
                        label: okCancelLabel,
                        className: 'btn-default'

                    }
                },
                onEscape: function() {}
            });
        } else {
            bootbox.dialog({
                backdrop: 'static',
                title: title,
                message: message,
                buttons: {
                    cancel: {
                        label: 'Close',
                        className: 'btn-default'
                    }
                },
                onEscape: function() {}
            });
        }
    });
});

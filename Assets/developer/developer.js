var leftMenuHandler = {
    mobileWidth: 767,
    isSticky: null,
    isStickyActive: false,
    isDesktopMenuTemplate: null,
    slideOut: null,
    isDropDownMobile: null,
    isLogoAnimationInProgress: false,
    logoHeightAmend: 0,
    scrollHandler: null,

    initialization: function () {
        this.initializeStarters();
        this.initializeListeners();
        this.sandbox();
    },
    initializeStarters: function () {
        this.navbarInit();
        this.stickyInit();
        this.controlMenuInit();
        this.mangoSlideOutInit();
        this.dropDownInit();

        this.scrollHandler = new ScrollHandler(this);
    },
    initializeListeners: function () {
        this.resizeListener();
    },
    resizeListener: function () {
        var _this = this;

        $(window).resize(function () {
            _this.navbarListener();
            _this.stickyResizeListener();
            _this.controlMenuResize();
            _this.mangoSlideOutResize();
            _this.dropDownResize();
            _this.scrollHandler.resizeListener();
        });
    },

    //handle collapse of top menu and left menu
    navbarInit: function () {
        if (this.isMobile()) {
            $('#navbar').addClass('fixed-header');
        }
    },
    navbarListener: function () {
        if (this.isMobile())
            $('#navbar').addClass('fixed-header');
        else
            $('#navbar').removeClass('fixed-header');
    },

    //handle sticky.js for left menu in no mobile mode
    stickyInit: function () {
        var _this = this;
        var menu = $(".menu-content");

        if ($(document).width() > this.mobileWidth) {
            $(menu).sticky();
            _this.stickyEventsHandler(menu);
            _this.isSticky = true;
        } else _this.isSticky = false;
    },
    stickyResizeListener: function () {
        var _this = this;
        var menu = $(".menu-content");

        if ($(document).width() > _this.mobileWidth) {
            if (!_this.isSticky) {
                $(menu).sticky();
                _this.stickyEventsHandler(menu);
                _this.isSticky = true;
            }
        } else {
            if (_this.isSticky) {
                $(menu).unstick();
                _this.isSticky = false;

            }
        }
    },
    stickyEventsHandler: function (menu) {
        var _this = this;

        $(menu).on('sticky-start', function () {
            $('.menu-logo').slideDown({
                progress: function () {
                    $(menu).resize();
                },
                start: function () {
                    _this.isLogoAnimationInProgress = true;
                },
                done: function () {
                    _this.isLogoAnimationInProgress = false;
                }
            });
            $('.logo-padding').slideUp();
            _this.isStickyActive = true;
        });
        $(menu).on('sticky-end', function () {
            $('.menu-logo').slideUp({
                progress: function () {
                    $(menu).resize();
                },
                start: function () {
                    _this.isLogoAnimationInProgress = true;
                },
                done: function () {
                    _this.isLogoAnimationInProgress = false;
                }
            });
            $('.logo-padding').slideDown();
            _this.isStickyActive = false;
        });
    },

    //handle of control menu
    controlMenuInit: function () {
        this.putMenuTemplate();
        $('.logo-padding').show();
    },
    controlMenuResize: function () {
        if (this.isMobile() && this.isDesktopMenuTemplate) {
            deleteItems();
            this.putMenuTemplate();
        } else if (!this.isMobile() && !this.isDesktopMenuTemplate) {
            deleteItems();
            this.putMenuTemplate();
        }

        function deleteItems() {
            var menu = $('.menu-items-block');

            if (menu.length)
                $(menu).remove();
        }
    },
    putMenuTemplate: function () {
        var menuBlock = $('.menu-block');

        var template = $('#menu-template').html();
        var tmpl = _.template(template);

        if (!this.isMobile()) {
            $(menuBlock).hide();
            $('.menu-logo').after(tmpl());
            $(menuBlock).fadeIn();
            this.isDesktopMenuTemplate = true;
        } else {
            $('#mobile-menu header').append(tmpl());
            this.isDesktopMenuTemplate = false;
        }
    },

    //handle mango slideOut
    mangoSlideOutInit: function () {
        if (!this.isMobile()) return;

        this.mangoSlideOutCreate();
    },
    mangoSlideOutResize: function () {
        if (this.isMobile() && _.isNull(this.slideOut)) {
            this.mangoSlideOutCreate();
        } else if (!this.isMobile() && this.slideOut) {
            this.slideOut.destroy();
            this.slideOut = null;

            $('#mobile-menu').removeClass('slideout-menu slideout-menu-left');
            $('#panel').removeClass('slideout-panel slideout-panel-left');
        }
    },
    mangoSlideOutCreate: function () {
        var _this = this;

        this.slideOut = new Slideout({
            'panel': document.getElementById('panel'),
            'menu': document.getElementById('mobile-menu'),
            'padding': 256,
            'tolerance': 70
            //'side': 'right'
        });

        var fixed = document.querySelector('.fixed-header');

        this.slideOut.on('translate', function (translated) {
            fixed.style.transform = 'translateX(' + translated + 'px)';
        });

        this.slideOut.on('beforeopen', function () {
            fixed.style.transition = 'transform 300ms ease';
            fixed.style.transform = 'translateX(256px)';
        });

        this.slideOut.on('beforeclose', function () {
            fixed.style.transition = 'transform 300ms ease';
            fixed.style.transform = 'translateX(0px)';
        });

        this.slideOut.on('open', function () {
            fixed.style.transition = '';
        });

        this.slideOut.on('close', function () {
            fixed.style.transition = '';
        });
    },
    //handle drop down menu and interact menu with mango slide out
    //(slide out do not slide fixed drop down menu, even if he invisible, that methods fix it)
    dropDownInit: function () {
        if (!this.isMobile()) {
            this.isDropDownMobile = true;
            return false;
        }

        this.isDropDownMobile = false;

        var block = $(".navbar-info");
        var noticeBlock = $('#notice-dropdown');
        var timer;

        $(block).on("show.bs.dropdown", function (event) {
            clearTimeout(timer);

            $(noticeBlock).css({
                position: 'fixed'
            })
        });

        $(block).on("hidden.bs.dropdown", function (event) {
            timer = setTimeout(function () {
                $(noticeBlock).css({
                    position: 'absolute'
                })
            }, 200);
        });
    },
    dropDownResize: function () {
        var block = $(".navbar-info");
        var noticeBlock = $('#notice-dropdown');
        var timer;

        if (this.isMobile() && !this.isDropDownMobile) {
            this.isDropDownMobile = true;

            $(block).on("show.bs.dropdown", function (event) {
                clearTimeout(timer);
                $(noticeBlock).css({
                    position: 'fixed'
                })
            });

            $(block).on("hidden.bs.dropdown", function (event) {
                timer = setTimeout(function () {
                    $(noticeBlock).css({
                        position: 'absolute'
                    });
                }, 200);
            });

            if (isDropDownOpen(block)) {
                $(noticeBlock).css({
                    position: 'fixed'
                })
            }

        } else if (!this.isMobile() && this.isDropDownMobile) {
            this.isDropDownMobile = false;

            $(block).off("show.bs.dropdown");
            $(block).off("hidden.bs.dropdown");

            $(noticeBlock).css({
                position: 'absolute'
            });
        }

        function isDropDownOpen(noticeBlock) {
            return $(noticeBlock).hasClass('open');
        }
    },
    isMobile: function () {
        return $(window).width() <= this.mobileWidth
    },
    sandbox: function () {

    }
};

/**
 * Object, that handle work with scroll bar in left menu
 * @param aggregator leftMenuHandler
 * @constructor
 */
function ScrollHandler(aggregator) {

    var _this = this;
    var menuHandler = aggregator;
    var menu = $('.menu-content');
    var logo = $('.menu-logo');
    var scrollBlock = $(".scroll-block");
    var buttonBlock = $('.button-block div');
    var logoPadding = $('.logo-padding');
    var userBlock = $('.user-block');
    var header = $('#navbar');

    var scrollBarMCustom = null;

    var isPreviousMobile = menuHandler.isMobile();

    constructor();

    function constructor() {
        scrollBarInit();
        scrollListener();
    }

    this.resizeListener = function () {
        widthResize();
        heightResize();
    };

    function scrollBarInit() {

        if (menuHandler.isMobile()) return false;

        scrollSizeCalculator();
        scrollBarMCustom = $(scrollBlock).mCustomScrollbar();
    }

    function scrollListener() {
        $(document).on('scroll', function () {
            scrollSizeCalculator();
        });

        //$(document).on('scroll', _.debounce(scrollSizeCalculator(), 500));
    }

    function widthResize() {
        if (menuHandler.isMobile() && !isPreviousMobile) {
            $(scrollBlock).mCustomScrollbar('destroy');
            isPreviousMobile = true;
        } else if (!menuHandler.isMobile() && isPreviousMobile) {

            scrollBlock = $(".scroll-block");
            buttonBlock = $('.button-block div');
            logoPadding = $('.logo-padding');
            userBlock = $('.user-block');

            window.scrollTo(1, 0);
            scrollSizeCalculator();
            $(scrollBlock).mCustomScrollbar();
            $(scrollBlock).mCustomScrollbar("update");
            isPreviousMobile = false;
        }
    }

    function heightResize() {
        if (menuHandler.isMobile()) return false;
        scrollSizeCalculator();
    }

    function scrollSizeCalculator() {
        if (!isScrollNeeded()) return false;

        if (isMenuSticking()) {
            $(scrollBlock).css({
                height: $(window).height() -
                ($(logo).height() + menuHandler.logoHeightAmend) -
                $(buttonBlock).height() -
                $(userBlock).height()
            });
        } else {
            $(scrollBlock).css({
                height: $(window).height() -
                ($(header).height() + _.parseInt($(header).css('margin-bottom')) - $(window).scrollTop()) -
                $(buttonBlock).height() -
                _.parseInt($(logoPadding).css('padding-top')) -
                $(userBlock).height()
            });
        }
    }

    function isMenuSticking() {
        return $(document).scrollTop() > ($(header).height() + _.parseInt($(header).css('margin-bottom')));
    }

    function isScrollNeeded() {
        if (!isMenuSticking()) {
            return $('.menu-content').height() > ($(window).height() -
                    $(header).height() +
                    _.parseInt($(header).css('margin-bottom')) -
                    $(window).scrollTop()
                );
        } else {
            return $('.menu-content').height() > $(window).height();
        }
    }
}

$(function () {
    leftMenuHandler.initialization();

    //Sortable.create(sort, {
    //    animation: 150
    //});
});
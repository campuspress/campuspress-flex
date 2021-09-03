var cpSchoolThemeHelpers = (function ($) {
    var methods = {};

    /* Disabled - parellax script needs to be changed.
    methods.manageParallaxHeader = function (destroy) {
        if(typeof cpSchoolData.parallaxHeader !== 'object' && !destroy) {
            var headerImage = document.querySelectorAll('.hero-image-holder img');
            cpSchoolData.parallaxHeader = new simpleParallax(headerImage, {
                delay: 2,
                orientation: 'up',
                scale: 1.3,
            });
        }
        if(cpSchoolData.parallaxHeader && destroy) {
            cpSchoolData.parallaxHeader.destroy();
            cpSchoolData.parallaxHeader = false;
        }
    };
    */

    methods.setCSSVar = function (varName, value) {
        if (getComputedStyle(document.documentElement).getPropertyValue(varName) != value) {
            document.documentElement.style.setProperty(varName, value);
        }
    };

    methods.getNavbarHeight = function () {
        var navbarMain = $('#navbar-main');

        if (navbarMain.length) {
            return navbarMain.outerHeight();
        }

        return 0;
    };

    methods.getNavbarBrandHolderHeight = function () {
        var navbarBrandHolder = $('#navbar-main .navbar-brand-holder');

        if (navbarBrandHolder.length) {
            return navbarBrandHolder.outerHeight();
        }

        return 0;
    };

    methods.getHeaderDropboxGap = function (strict) {
        var navbarMainHeight = methods.getNavbarHeight();
        var navbarMainBrand = $('#wrapper-navbar-main.navbar-style-dropbox .navbar-brand');

        if (navbarMainHeight && navbarMainBrand.length) {
            var difference = navbarMainBrand.outerHeight() - navbarMainHeight;
            if (strict) {
                if (difference > 0) {
                    return difference;
                }
            }
            else {
                var margin = 3 * 16;
                if (difference > margin) {
                    return difference - margin;
                }
            }
        }

        return 0;
    };

    methods.getHeaderButtonsMenuInlineWidth = function () {
        var navbarMainButtonsMenu = $('#navbar-main-nav-buttons');

        if (navbarMainButtonsMenu.length) {
            var navbarMainContainerWidth = $('#navbar-main .navbar-container').width();
            var navbarMainNavWidth = $('#menu-main-desktop').width();

            var navbarMainButtonsMenuOuterWidth = navbarMainButtonsMenu.outerWidth();
            if (navbarMainContainerWidth > (navbarMainButtonsMenuOuterWidth * 2 + navbarMainNavWidth)) {
                return navbarMainButtonsMenuOuterWidth;
            }
        }

        return 0;
    };

    methods.setSidebarStickness = function () {
        //TODO we should check that with js var
        if ($('body').hasClass('sidebars-check-sticky')) {
            $(".sidebar-widget-area-content").each(function (index) {
                if ($(window).height() > ($(this).outerHeight() + parseInt($(this).css('top')))) {
                    $(this).addClass('sidebar-sticky');
                }
                else {
                    $(this).removeClass('sidebar-sticky');
                }
            });
        }
    };

    methods.enableDropdownAnimation = function () {
        // Support for transitions in dropdown.
        $('.dropdown').on('shown.bs.dropdown', function (action) {
            var droprown = $(this);
            setTimeout(function () {
                droprown.addClass('shown');
            }, 10);
        });
        $('.dropdown-menu').on('transitionend', function (e) {
            if ($(e.target).hasClass('dropdown-menu')) {
                if (!$(this).hasClass('show')) {
                    $(this).parent().removeClass('shown');
                }
            }
        });
    };

    methods.enableAlertDismissal = function (alertBar) {
        if (alertBar.length) {
            var alertVer = alertBar.data('ver');

            alertBar.find('[data-dismiss="alert"]').click(function (e) {
                Cookies.set('site_alert_bar_dismiss_ver', alertVer, { expires: 365 });
            });
        }
    };

    methods.enableHoverMenu = function () {
        // Handles dropdown in navbar.
        $('.navbar-nav').on('touchend mouseenter focusin', '.dropdown', function (e) {
            var dropdown = this;
            var dropdownChild = $(dropdown).children('.dropdown-toggle[aria-expanded="false"]');
            if (dropdownChild.length) {
                // Makes first touch open submenu and second open the link.
                if (e.type == 'touchend') {
                    e.preventDefault();
                    $('.navbar-nav').off('mouseenter focusin', '.dropdown');
                }

                //Timeout will make fast mouse swipes through menu items not trigger it
                setTimeout(function () {
                    if (e.type !== 'mouseover' || $(dropdown).is(':hover')) {
                        dropdownChild.dropdown('show');
                        dropdownChild.off("click");
                        if (e.type === 'mouseenter' || e.type === 'mouseover') {
                            dropdownChild.trigger('blur');
                        }
                    }
                }, e.type === 'mouseenter' ? 150 : 0);
            }
        });
        $('.navbar-nav').on('mouseleave focusout', '.dropdown', function (e) {
            var dropdown = this;

            // Timeout will give time to move into the dropdown menu.
            setTimeout(function () {
                if (!$(dropdown).is(':hover')) {
                    if (e.type !== 'focusout' || !dropdown.contains(e.relatedTarget)) {
                        var dropdownChild = $(dropdown).children('.dropdown-toggle[aria-expanded="true"]');
                        if (dropdownChild.length) {
                            dropdownChild.dropdown('hide');
                            dropdownChild.attr('aria-expanded', false);
                        }
                    }
                }
            }, e.type === 'mouseleave' ? 300 : 0);
        });

        // Handles sidebar menu based on "collapse".
        $('.nav:not(.navbar-nav)').on('touchend mouseenter focusin', '.dropdown', function (e) {
            var dropdown = this;
            var dropdownChild = $(dropdown).children('.dropdown-toggle[aria-expanded="false"]');
            if (dropdownChild.length) {
                // Makes first touch open submenu and second open the link.
                if (e.type == 'touchend') {
                    e.preventDefault();
                    $('.nav:not(.navbar-nav)').off('mouseenter focusin', '.dropdown-toggle');
                }

                //Timeout will make fast mouse swipes through menu items not trigger it
                setTimeout(function () {
                    if (e.type !== 'mouseover' || $(dropdown).is(':hover')) {
                        $(dropdownChild.data('target')).collapse('show');
                        dropdownChild.attr('aria-expanded', 'true');
                    }
                }, e.type === 'mouseenter' ? 150 : 0);
            }
        });
        $('.nav:not(.navbar-nav)').on('mouseleave focusout', '.dropdown', function (e) {
            if (!$(this).is(':hover')) {
                if (e.type !== 'focusout' || !this.contains(e.relatedTarget)) {
                    var dropdownChild = $(this).children('.dropdown-toggle[aria-expanded="true"]');
                    if (dropdownChild.length) {
                        $(dropdownChild.data('target')).collapse('hide');
                        dropdownChild.attr('aria-expanded', 'false');
                    }
                }
            }
        });

        $('.nav:not(.navbar-nav)').on('click', '.dropdown-toggle[href="#"]', function (e) {
            e.preventDefault();
        });
    };

    return methods;
})(jQuery);
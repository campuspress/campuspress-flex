(function ($) {
	// Calculates necessary things on resize.
	$(window).on('resize', function () {
		var navbarMainHeight = cpSchoolThemeHelpers.getNavbarHeight();
		cpSchoolThemeHelpers.setCSSVar(`--header-main-height`, navbarMainHeight + 'px');

		var headerDropboxGap = cpSchoolThemeHelpers.getHeaderDropboxGap(false);
		cpSchoolThemeHelpers.setCSSVar(`--header-main-gap-height`, headerDropboxGap + 'px');

		var headerButtonsMenuWidth = cpSchoolThemeHelpers.getHeaderButtonsMenuInlineWidth();
		if (headerButtonsMenuWidth) {
			cpSchoolThemeHelpers.setCSSVar(`--header-main-buttons-menu-width`, headerButtonsMenuWidth + 'px');
		}

		var navbarMainBrandHolderHeight = cpSchoolThemeHelpers.getNavbarBrandHolderHeight();
		if (navbarMainBrandHolderHeight) {
			cpSchoolThemeHelpers.setCSSVar(`--header-main-navbar-brand-holder-height`, navbarMainBrandHolderHeight + 'px');
		}

		/* Disabled - parellax script needs to be changed.
		if(cpSchoolData.parallaxHeader) {
			cpSchoolThemeHelpers.manageParallaxHeader(false);
		}
		*/

		cpSchoolThemeHelpers.setSidebarStickness();
	});
	// Lets trigger resize action when page is ready.
	$(document).ready(function () {
		$(window).trigger('resize');
	});

	// Enables dropdown animations.
	cpSchoolThemeHelpers.enableDropdownAnimation();

	// Enables dismissal of alerts.
	cpSchoolThemeHelpers.enableAlertDismissal( $('#site-alert') );

	// Enables hover menu.
	if ($('body').hasClass('navbar-hover-enabled')) {
		cpSchoolThemeHelpers.enableHoverMenu();
	}

	// Add sticky class to nav.
	if ('IntersectionObserver' in window) {
		var observer = new IntersectionObserver(function (entries) {
			if ($('body').hasClass('navbar-main-sticky-top')) {
				if (entries[0].intersectionRatio === 0) {
					$("#wrapper-navbar-main-top").addClass('intersected');
					$('#wrapper-navbar-main').addClass('navbar-sticks');
				}
				else if (entries[0].intersectionRatio === 1) {
					$("#wrapper-navbar-main-top").removeClass('intersected');
					$('#wrapper-navbar-main').removeClass('navbar-sticks');
				}
			}
		}, { threshold: [0, 1] });
		observer.observe(document.querySelector("#wrapper-navbar-main-top"));
	}

	// Focus search input after opening search modal.
	$('#modal-search').on('shown.bs.modal', function () {
		$('#modal-search').find('input').first().trigger('focus');
	});

	// Show alert popup if its configured.
	var alertPopup = $('#modal-alert');
	if (alertPopup.length) {
		alertPopup.modal('show');
		alertPopup.on('hidden.bs.modal', function (e) {
			var alert_ver = alertPopup.data('ver');
			Cookies.set('site_alert_popup_dismiss_ver', alert_ver, { expires: 365 });
		});
	}

	// Handles basic animations on the site.
	if (cpSchoolData.animations) {
		$('.entry-content > .alignfull, .entry-content > .alignwide, .entry-content > .aligncenter').attr('data-aos', 'fade-up');
		$('.entry-content > .alignleft').attr('data-aos', 'fade-right');
		$('.entry-content > .alignright').attr('data-aos', 'fade-left');

		AOS.init({
			offset: 150,
			delay: 50,
			duration: 800,
			once: true,
			disable: function () {
				if (window.document.documentMode <= 11) {
					return true
				}
				else {
					return false;
				}
			}
		});
	}

	// This will make vars in css work in IE11.
	if (typeof cssVars === "function") {
		cssVars({
			preserveStatic: false
		});
	}
})(jQuery);
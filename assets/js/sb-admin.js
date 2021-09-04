// Setting up Popper and jQuery.
import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

/*!
 * Start Bootstrap - SB Admin 2 v4.1.3 (https://startbootstrap.com/theme/sb-admin-2)
 * Copyright 2013-2020 Start Bootstrap
 * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin-2/blob/master/LICENSE)
 */

(function($) {
    "use strict"; // Start of use strict

    // Toggle the side navigation
    $("#sidebarToggle, #sidebarToggleTop").on('click', () => {
        $("body").toggleClass("sidebar-toggled");
        $(".sidebar").toggleClass("toggled");
        if ($(".sidebar").hasClass("toggled"))
            $('.sidebar .collapse').collapse('hide');
    });

    // Close any open menu accordions when window is resized below 768px
    $(window).resize(() => {
        if ($(window).width() < 768)
            $('.sidebar .collapse').collapse('hide');

        // Toggle the side navigation when window is resized below 480px
        if ($(window).width() < 480 && !$(".sidebar").hasClass("toggled")) {
            $("body").addClass("sidebar-toggled");
            $(".sidebar").addClass("toggled");
            $('.sidebar .collapse').collapse('hide');
        }
    });

    // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
    $('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', (e) => {
        if ($(window).width() > 768) {
            const e0 = e.originalEvent,
                delta = e0.wheelDelta || -e0.detail;
            this.scrollTop += (delta < 0 ? 1 : -1) * 30;
            e.preventDefault();
        }
    });

    // Scroll to top button appear
    $(document).on('scroll', () => $(this).scrollTop());

    // Smooth scrolling using jQuery easing
    $(document).on('click', 'a.scroll-to-top', () => {
        $(this);
        $("html, body").animate({ scrollTop: 0 }, "slow");
    });

})(jQuery); // End of use strict

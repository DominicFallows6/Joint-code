define(['jquery'], function ($) {
    "use strict";

    return function (config, node) {

        var windowWidth, resizeTime;

        // Get initial page width
        windowWidth = $(window).width();

        // Create the hover effect on the USPs
        function activateHover() {
            $(node).mouseenter(function () {
                $(this).children("span").show();
            }).mouseleave(function () {
                $(this).children("span").hide();
            });
        }

        // Check the width of the window and add a class accordingly
        function checkDesktop(){
            windowWidth = $(window).width();
            if (windowWidth >= 768) {
                activateHover();
            } else {
                $(node).unbind('mouseenter').unbind('mouseleave');
            }
        }

        // Run activateHover on page load
        checkDesktop();

        // Listen for window resize and run activateHover when resizing has finished
        // (this is for performance reasons)
        $(window).resize(function() {
            clearTimeout(resizeTime);
            resizeTime = setTimeout(doneResizing, 500);
        });

        function doneResizing(){
            checkDesktop();
        }


    };


});

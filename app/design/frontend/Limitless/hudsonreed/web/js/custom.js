define(['jquery'], function($) {

    "use strict";

    var toggle = function() {
        if ($(window).width() < 640) {
            $("ul.footer .hdr").click(function () {
                $(this).toggleClass("active");
                $(this).parent().find("li.footer-toggle").toggleClass("active");
            });
        }
    };

    var toggleUsp = function() {
        if($(window).width() < 768) {
            $('.usp-reasons').appendTo('.nav-sections');
        } else if($(window).width() >= 768){
            $('.usp-reasons').insertAfter('.nav-sections');
        }
    };

    $(function(){
        toggle();
        toggleUsp();
        $(window).on("resize", function() {
            toggle();
            toggleUsp();
        });
    });

});
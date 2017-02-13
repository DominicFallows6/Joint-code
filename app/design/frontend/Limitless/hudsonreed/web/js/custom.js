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

    $(function(){
        toggle();
        $(window).on("resize", function() {
            toggle();
        });
    });

});
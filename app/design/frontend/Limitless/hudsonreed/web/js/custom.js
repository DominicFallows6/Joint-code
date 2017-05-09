define(['jquery'], function($) {

    "use strict";

    var toggle = function() {
        $("ul.footer .hdr").click(function () {
            $(this).toggleClass("active");
            $(this).parent().find("li.footer-toggle").toggleClass("active");
        });
    };

    toggle();

});
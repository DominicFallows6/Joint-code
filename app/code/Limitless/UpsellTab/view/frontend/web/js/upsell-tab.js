define(['jquery'], function($) {

    "use strict";

    var toggleTabs = function() {
        var tabs = $('.product-tabs ul li');
        $(tabs).click(function(e) {
            if($(this).hasClass('active')) {
                $(this).addClass('active');
            } else {
                $(this).toggleClass('active').siblings().removeClass('active');
            }
            e.preventDefault();
        });

        $('.product-details').click(function(e) {
            $('.upsell').hide();
            $('.product-info-content').show();
            e.preventDefault();
        });

        $('.up-sells').click(function(e) {
            $('.product-info-content').hide();
            $('.upsell').show();
            e.preventDefault();
        });
    };

    toggleTabs();

});
define(['ko', 'jquery'], function (ko, $) {
    "use strict";
    return function (config) {

        function createCookie(name, value, days) {
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                var expires = "; expires=" + date.toGMTString();
            }
            else var expires = "";
            document.cookie = name + "=" + value + expires + "; path=/";
        }

        function readCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return false;
        }

        function eraseCookie(name) {
            createCookie(name, "", -1);
        }

        /**************************************************************
         * Actual code below
         *************************************************************/

        var cookieLock = false;

        function listenCookieChange(cookieName, callback) {
            setInterval(function () {
                if (readCookie(cookieName) && cookieLock === false) {
                    cookieLock = true;
                    return callback();
                }
            }, 100);
        }

        listenCookieChange('add_to_cart', function () {
            var googleAnalyticsUniversalCart = new GoogleAnalyticsUniversalCart();
            googleAnalyticsUniversalCart.parseAddToCartCookies();
            eraseCookie('add_to_cart');
            cookieLock = false;
        });

    }
});
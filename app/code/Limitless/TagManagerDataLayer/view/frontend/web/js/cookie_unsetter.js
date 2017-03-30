define(['jquery', 'mage/cookies'], function ($) {
    "use strict";
    return function (config) {
        var affiliateCookieName = config.cookie_name;

        if ($.mage.cookies.get(affiliateCookieName)) {
            $.mage.cookies.clear(affiliateCookieName);
        }
    }
});
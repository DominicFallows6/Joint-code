define(['jquery', 'mage/cookies'], function ($) {
    "use strict";
    return function (config) {
        var categoryName = config.category_name;
        var affiliateCookieName = 'LDG_GA_Category';

        $.mage.cookies.set(affiliateCookieName, categoryName);
    }
});
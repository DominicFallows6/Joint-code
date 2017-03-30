define(['jquery', 'mage/cookies'], function ($) {
    "use strict";
    return function (config) {
        var affiliateUrlParamName = config.url_param_name;
        var affiliateCookieName = config.cookie_name;

        var url = window.location.search;
        if (url.indexOf('?'+affiliateUrlParamName+'=') !== -1 || url.indexOf('&'+affiliateUrlParamName+'=') !== -1) {
            var urlParams = url.split('?');
            if (typeof urlParams[1] !== 'undefined') {
                var urlIndividualParams = urlParams[1].split('&');
                for (var i = 0; i < urlIndividualParams.length; i++) {
                    var pair = urlIndividualParams[i].split("=");
                    if (pair[0] == affiliateUrlParamName) {
                        $.mage.cookies.set(affiliateCookieName, pair[1]);
                    }
                }
            }
        }
    }
});
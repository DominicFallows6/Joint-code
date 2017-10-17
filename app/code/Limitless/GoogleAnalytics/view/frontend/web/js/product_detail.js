define(['ko', 'jquery', 'mage/cookies'], function (ko, $) {
    "use strict";
    return function (config) {
        var productDetailId = config.product_detail_id;
        var productDetailName = config.product_detail_name;

        var productDetailCategory = config.product_detail_category;
        var productDetailAllowCookies = config.product_detail_allow_category_cookie;
        var productDetailDefaultCookieCategory = config.product_detail_category_cookie_default;

        var productDetailBrand = config.product_detail_brand;
        var productDetailVariant = config.product_detail_variant;
        var productDetailExtras = config.product_detail_extras;
        var affiliateCookieName = 'LDG_GA_Category';

        if (productDetailAllowCookies === "1") {
            var appendToCategory = productDetailDefaultCookieCategory;

            if ($.mage.cookies.get(affiliateCookieName)) {
                var cookieValue = $.mage.cookies.get(affiliateCookieName);
                if ((cookieValue instanceof String || typeof cookieValue === 'string')
                    && 0 !== cookieValue.length) {
                    appendToCategory = cookieValue;
                }
            }
            productDetailCategory = productDetailCategory + '/' + appendToCategory;
        }

        var productObject =
            {
                'id': productDetailId,
                'name': productDetailName,
                'category': productDetailCategory,
                'brand': productDetailBrand
            };

        if (productDetailVariant && productDetailVariant.length > 0) {
            productObject['variant'] = productDetailVariant;
        }

        var splitExtras = productDetailExtras.split(',');
        splitExtras.forEach(function(extra) {
            var splitAttribute = extra.split('::');
            if(splitAttribute.size() == 2) {
                productObject[splitAttribute[0]] = splitAttribute[1];
            }
        });



        dataLayer.push({
            'event' : 'productDetail',
            'ecommerce': {
                'detail': {
                    'products': [productObject]
                },
                'impressions' : []
            }
        });
    }
});
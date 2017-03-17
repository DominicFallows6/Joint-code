/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'moment'
    ],
    function ($, Component, quote, moment) {
        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/summary/shipping'
            },
            locale: moment().localeData()._abbr,
            quoteIsVirtual: quote.isVirtual(),
            totals: quote.getTotals(),
            getShippingMethodTitle: function() {
                if (!this.isCalculated()) {
                    return '';
                }
                var shippingMethodTitle = '';
                var shippingMethod = quote.shippingMethod();
                if(shippingMethod) {
                    if(shippingMethod.date) {
                        var deliveryDate = moment(shippingMethod.date);
                        shippingMethodTitle = deliveryDate.format("MMMM Do YYYY") + ' (' + shippingMethod.carrier_title + ')';
                    } else {
                        shippingMethodTitle = shippingMethod.carrier_title + " - " + shippingMethod.method_title;
                    }
                }

                return shippingMethodTitle;
            },
            isCalculated: function() {
                return this.totals() && this.isFullMode() && null != quote.shippingMethod();
            },
            getValue: function() {
                if (!this.isCalculated()) {
                    return this.notCalculatedMessage;
                }
                var price =  this.totals().shipping_amount;
                return this.getFormattedPrice(price);
            }
        });
    }
);
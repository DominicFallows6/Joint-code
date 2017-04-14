/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/sidebar',
        'moment'
    ],
    function($, Component, quote, stepNavigator, sidebarModel, moment) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/shipping-information'
            },

            isVisible: function() {
                return !quote.isVirtual() && stepNavigator.isProcessed('shipping');
            },

            getShippingMethodTitle: function() {
                this.ucwords = function (str) {
                    return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                        return $1.toUpperCase();
                    });
                };
                var shippingMethodTitle = '';
                var shippingMethod = quote.shippingMethod();
                if(shippingMethod) {
                    if(shippingMethod.date) {

                        var countryId = quote.shippingAddress().countryId;
                        this.siteLocale = countryId.toLowerCase() + '-' + countryId;

                        var formatDeliveryDate = new Date(moment(shippingMethod.date));
                        var options = { year: 'numeric', month: 'short', day: '2-digit'};
                        var translatedDeliveryDate = formatDeliveryDate.toLocaleString(this.siteLocale, options);

                        shippingMethodTitle = this.ucwords(translatedDeliveryDate) + ' (' + shippingMethod.carrier_title + ')';
                    } else {
                        shippingMethodTitle = shippingMethod.carrier_title + " - " + shippingMethod.method_title;
                    }
                }

                return shippingMethodTitle;
            },

            back: function() {
                sidebarModel.hide();
                stepNavigator.navigateTo('shipping');
            },

            backToShippingMethod: function() {
                sidebarModel.hide();
                stepNavigator.navigateTo('shipping', 'opc-shipping_method');
            }
        });
    }
);

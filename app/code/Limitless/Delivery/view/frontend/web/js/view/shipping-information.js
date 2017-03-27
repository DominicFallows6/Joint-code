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
    function($, Component, quote, stepNavigator, sidebarModel) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/shipping-information'
            },

            isVisible: function() {
                return !quote.isVirtual() && stepNavigator.isProcessed('shipping');
            },

            getShippingMethodTitle: function() {
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

/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'ko',
        'Magento_Checkout/js/view/payment/default'
    ],
    function (ko, Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Limitless_CheckoutPaymentInstructions/payment/banktransfer'
            },
            /**
             * Get value of instruction field.
             * @returns {String}
             */
            getInstructions: function () {
                return window.checkoutConfig.payment.instructions[this.item.method];
            },
            /**
             * Get value of checkout instruction field.
             * @returns {String}
             */
            getCheckoutInstructions: function () {
                return window.checkoutConfig.payment.checkout_instructions[this.item.method];
            }
        });
    }
);

/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/modal',
    'jquery/ui'
], function($, $t) {
    "use strict";

    $.widget('Limitless_BasketPopUp.customAddToCart', {

        options: {
            processStart: null,
            processStop: null,
            bindSubmit: true,
            minicartSelector: '[data-block="minicart"]',
            messagesSelector: '[data-placeholder="messages"]',
            oldPrice: '[data-price-type="oldPrice"] .price',
            finalPrice: '[data-price-type="finalPrice"] .price',
            productStatusSelector: '.stock.available',
            addToCartButtonSelector: '.action.tocart',
            productPageCartButton: '#product-addtocart-button',
            upsellProductCartButton: '.upsell .tocart',
            configurableProduct: '.product-options-wrapper .super-attribute-select',
            productPopUp: '.product-info-popup',
            addToCartButtonDisabledClass: 'disabled',
            addToCartButtonTextWhileAdding: '',
            addToCartButtonTextAdded: '',
            addToCartButtonTextDefault: ''
        },

        configurableProductPrices: function() {
            var self = this;
            var productOptionDropdown = $(this.options.configurableProduct);
            productOptionDropdown.on('change', function() {
                var oldPrice = $(self.options.oldPrice).html();
                var finalPrice = $(self.options.finalPrice).html();
                $('.product-pop-up .special-price').html(finalPrice);
                $('.product-pop-up .old-price').html(oldPrice);
                $('.product-pop-up .price').html(finalPrice);
            });
        },

        _create: function() {
            var productInfo = $('.product-pop-up .product-info');
            var productUpsells = $('.product-pop-up .upsells');
            var productUpsellsInfo = $('.product-pop-up .extras-info');
            var productOptions = $('.product-title .options');

            if (this.options.bindSubmit) {
                this._bindSubmit();
            }

            if ($(this.options.configurableProduct).length) {
                this.configurableProductPrices();
            }

            $(this.options.upsellProductCartButton).click(function() {
               var self = this;
               var upsellTitle = $(self).closest('.product-item-info').find('.product-item-name .product-item-link').html();
               var upsellImage = $(self).closest('.product-item-info').find('.product-item-photo .product-image-container').html();
               var upsellPrice = $(self).closest('.product-item-info').find('.price-box').html();
               setTimeout(function() {
                   $('.product-info-popup .extras-info .product-title').html(upsellTitle);
                   $('.product-info-popup .extras-info .product-image').html(upsellImage);
                   $('.product-info-popup .extras-info .product-price').html(upsellPrice);
                   $(productInfo).hide();
                   $(productUpsellsInfo).show();
               }, 1200);
            });

            $(this.options.productPageCartButton).click(function() {
                var productOptionsSelected = $('.product-options-wrapper .super-attribute-select option:selected');
                var productConfigOptions = [];
                productOptionsSelected.each(function() {
                    productConfigOptions.push($(this).text());
                });
                $(productOptions).html(productConfigOptions.join(', '));
                $(productInfo).show();
                $(productUpsells).show();
                $(productUpsellsInfo).hide();
            });
        },

        _bindSubmit: function() {
            var self = this;
            this.element.on('submit', function(e) {
                e.preventDefault();
                self.submitForm($(this));
            });
        },

        isLoaderEnabled: function() {
            return this.options.processStart && this.options.processStop;
        },

        showPopUp: function() {
            var popup = $(this.options.productPopUp).modal({
                type: 'popup',
                responsive: false,
                width: 500,
                innerScroll: true,
                clickableOverlay: true,
                modalClass: 'basket-pop-up',
                buttons: [{
                    text: 'Continue Shopping',
                    click: function () {
                        this.closeModal();
                    }
                }]
            });

            setTimeout(function() {
                popup.modal('openModal');
            }, 1000);
        },

        /**
         * Handler for the form 'submit' event
         *
         * @param {Object} form
         */
        submitForm: function (form) {
            var addToCartButton, self = this;

            if (form.has('input[type="file"]').length && form.find('input[type="file"]').val() !== '') {
                self.element.off('submit');
                // disable 'Add to Cart' button
                addToCartButton = $(form).find(this.options.addToCartButtonSelector);
                addToCartButton.prop('disabled', true);
                addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
                form.submit();
            } else {
                self.ajaxSubmit(form);
            }
        },

        ajaxSubmit: function(form) {
            var self = this;
            $(self.options.minicartSelector).trigger('contentLoading');
            self.disableAddToCartButton(form);

            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: 'post',
                dataType: 'json',
                beforeSend: function() {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStart);
                    }
                },
                success: function(res) {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }

                    if (res.backUrl) {
                        window.location = res.backUrl;
                        return;
                    }
                    if (res.messages) {
                        $(self.options.messagesSelector).html(res.messages);
                    }
                    if (res.minicart) {
                        $(self.options.minicartSelector).replaceWith(res.minicart);
                        $(self.options.minicartSelector).trigger('contentUpdated');
                    }
                    if (res.product && res.product.statusText) {
                        $(self.options.productStatusSelector)
                            .removeClass('available')
                            .addClass('unavailable')
                            .find('span')
                            .html(res.product.statusText);
                    }
                    self.enableAddToCartButton(form);
                    self.showPopUp();
                }
            });
        },

        disableAddToCartButton: function(form) {
            var addToCartButtonTextWhileAdding = this.options.addToCartButtonTextWhileAdding || $t('Adding...');
            var addToCartButton = $(form).find(this.options.addToCartButtonSelector);
            addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
            addToCartButton.find('span').text(addToCartButtonTextWhileAdding);
            addToCartButton.attr('title', addToCartButtonTextWhileAdding);
        },

        enableAddToCartButton: function(form) {
            var addToCartButtonTextAdded = this.options.addToCartButtonTextAdded || $t('Added');
            var self = this,
                addToCartButton = $(form).find(this.options.addToCartButtonSelector);

            addToCartButton.find('span').text(addToCartButtonTextAdded);
            addToCartButton.attr('title', addToCartButtonTextAdded);

            setTimeout(function() {
                var addToCartButtonTextDefault = self.options.addToCartButtonTextDefault || $t('Add to Cart');
                addToCartButton.removeClass(self.options.addToCartButtonDisabledClass);
                addToCartButton.find('span').text(addToCartButtonTextDefault);
                addToCartButton.attr('title', addToCartButtonTextDefault);
            }, 1000);
        }
    });

    return $.Limitless_BasketPopUp.customAddToCart;
});
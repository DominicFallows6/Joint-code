/**  * Copyright © 2016 Magento. All rights reserved.  * See COPYING.txt for license details.  */ /*global define*/
define(
    [
        'jquery',
        'underscore',
        'Magento_Ui/js/form/form',
        'ko',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/create-shipping-address',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-address/form-popup-state',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Ui/js/modal/modal',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Magento_Checkout/js/checkout-data',
        'uiRegistry',
        'Limitless_Delivery/js/view/metapack-data',
        'moment',
        'mage/translate',
        'Magento_Checkout/js/model/shipping-rate-service'
    ],
    function ($,
              _,
              Component,
              ko,
              customer,
              addressList,
              addressConverter,
              quote,
              createShippingAddress,
              selectShippingAddress,
              shippingRatesValidator,
              formPopUpState,
              shippingService,
              selectShippingMethodAction,
              rateRegistry,
              setShippingInformationAction,
              stepNavigator,
              modal,
              checkoutDataResolver,
              checkoutData,
              registry,
              metapackData,
              moment,
              $t) {
        'use strict';
        var popUp = null;
        return Component.extend({
            defaults: {
                template: 'Limitless_Delivery/shipping'
            },
            locale: moment().localeData()._abbr,
            visible: ko.observable(!quote.isVirtual()),
            errorValidationMessage: ko.observable(false),
            isCustomerLoggedIn: customer.isLoggedIn,
            isFormPopUpVisible: formPopUpState.isVisible,
            isFormInline: addressList().length == 0,
            isNewAddressAdded: ko.observable(false),
            saveInAddressBook: 1,
            quoteIsVirtual: quote.isVirtual(),
            /** 
             * @return {exports} 
             * */
            initialize: function () {
                var self = this,
                    hasNewAddress,
                    fieldsetName = 'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset';
                this._super();
                shippingRatesValidator.initFields(fieldsetName);
                if (!quote.isVirtual()) {
                    stepNavigator.registerStep(
                        'shipping',
                        '',
                        $t('Shipping'),
                        this.visible, _.bind(this.navigate, this),
                        10
                    );
                }

                checkoutDataResolver.resolveShippingAddress();
                hasNewAddress = addressList.some(function (address) {
                    return address.getType() == 'new-customer-address';
                });
                this.isNewAddressAdded(hasNewAddress);
                this.isFormPopUpVisible.subscribe(function (value) {
                    if (value) {
                        self.getPopUp().openModal();
                    }
                });

                registry.async('checkoutProvider')(function (checkoutProvider) {
                    var shippingAddressData = checkoutData.getShippingAddressFromData();
                    if (shippingAddressData) {
                        checkoutProvider.set(
                            'shippingAddress',
                            $.extend({}, checkoutProvider.get('shippingAddress'), shippingAddressData)
                        );
                    }
                    checkoutProvider.on('shippingAddress', function (shippingAddressData) {
                        checkoutData.setShippingAddressFromData(shippingAddressData);
                    });
                });

                this.isDefaultSelected = ko.observable(true);
                this.submitWasClicked = ko.observable(false);
                this.isContinueToShippingMethodButtonVisible = ko.observable(true);
                this.isShippingMethodVisible = ko.computed(function () {
                    if (!this.visible()) {
                        return false;
                    }
                    if (this.submitWasClicked() && !this.source.get('params.invalid')) {
                        return true;
                    }
                    if (customer.isLoggedIn() && addressList().length) {
                        this.isContinueToShippingMethodButtonVisible(false);
                        return checkoutData.getSelectedShippingRate() || addressList().length;
                    }
                    return false;
                }.bind(this));

                this.mobileCheck = ko.computed(function(){
                    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                        return true;
                    } else {
                        return false;
                    }
                });

                this.preselectRate = function () {
                    var radioButtonsPremium = $('.rates-for-day input[type=radio]');
                    if(radioButtonsPremium.length === 1) {
                        radioButtonsPremium.click();
                    }
                };

                // *** WORK AROUND FOR MAGENTO CORE BUG MAGETWO-61384 ***
                this.enableRadioButtonsFix = function () {
                    $('.rates-for-day input[type=radio]').removeAttr('disabled');
                };

                function getDeliveryDatepickerData() {
                    return metapackData.getDeliveryOptions(shippingService.getShippingRates()());
                }

                var getDeliveryDatePickerBounds = function (deliveryDatepickerData) {
                    var daysFromToday = Object.keys(deliveryDatepickerData);
                    var lastDayFromToday = daysFromToday[daysFromToday.length - 1];
                    return lastDayFromToday;
                }.bind(this);

                var deliveryDatepickerOffset = ko.observable(1);

                this.nextWeek = function () {
                    deliveryDatepickerOffset(Math.min(
                        getDeliveryDatePickerBounds(getDeliveryDatepickerData()) - 7,
                        deliveryDatepickerOffset() + 7
                    ));
                };

                this.previousWeek = function () {
                    deliveryDatepickerOffset(Math.max(1, deliveryDatepickerOffset() - 7));
                };

                this.ucwords = function (str) {
                    return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                        return $1.toUpperCase();
                    });
                };

                this.deliveryDatepickerWindow = ko.computed(function () {
                    var week = [];
                    var deliveryDatepickerData = getDeliveryDatepickerData();
                    if (deliveryDatepickerData.length) {
                        var upperBound = getDeliveryDatePickerBounds(deliveryDatepickerData);
                        var startDay = Math.min(deliveryDatepickerOffset(), upperBound - 7);
                        var endDay = startDay + 7;
                        for (var day = startDay; day < endDay; day++) {
                            week.push(deliveryDatepickerData[day]);
                        }
                    }
                    return week;
                });

                this.selectDeliveryDateTranslate = function () {
                    return $t('Select a delivery date...');
                };

                var countryId = quote.shippingAddress().countryId;
                this.siteLocale = countryId.toLowerCase() + '-' + countryId;

                this.selectedRates = ko.observable();
                this.selectedRates.subscribe(function(newValue){

                    if(newValue == null){
                        this.isDefaultSelected(true);
                    } else {
                        this.isDefaultSelected(false);
                        this.mobileDeliveryRatesForDay.removeAll();
                        for (var rate in newValue.rates) {
                            this.mobileDeliveryRatesForDay.push(newValue.rates[rate]);
                        }
                    }

                }.bind(this));

                this.mobileDeliveryDatepickerWindow = ko.computed(function () {
                    var days = [];
                    var deliveryDatepickerData = getDeliveryDatepickerData();
                    if (deliveryDatepickerData.length > 0) {
                        for (var day = 0; day < deliveryDatepickerData.length; day++) {
                            var dateObj = deliveryDatepickerData[day];
                            if(typeof dateObj === 'object'){
                                if(dateObj.rates.length > 0) {
                                    days.push(deliveryDatepickerData[day]);
                                }
                            }
                        }
                    }
                    return days;
                });

                this.deliveryWindowStartDay = ko.computed(function () {
                    var today = new Date();
                    return new Date(today.getTime() + deliveryDatepickerOffset() * (24 * 60 * 60000));
                });

                this.deliveryWindowEndDay = ko.computed(function () {
                    return new Date(this.deliveryWindowStartDay().getTime() + 6 * (24 * 60 * 60000));
                }.bind(this));

                var dummyRate = {
                    method_code: '',
                    method_title: '',
                    amount: 0,
                    carrier_code: '',
                    carrier_title: ''
                };

                function padShippingRates(shippingGroup) {
                    var maxDeliveryOptions = metapackData.getMaxOptionsPerDay(shippingService.getShippingRates()());
                    var toFill = maxDeliveryOptions - shippingGroup.rates.length;
                    var emptyRates = Array.apply(null, new Array(toFill)).map(function(){return dummyRate});
                    return shippingGroup.rates.concat(emptyRates);
                }

                this.selectDeliveryDate = function (shippingGroup, event) {
                    var paddedRates = padShippingRates(shippingGroup);
                    var currentElement = $(event.currentTarget);
                    this.deliveryRatesForDay(paddedRates);
                    if(shippingGroup.rates.length > 0){
                        $('.rates-for-day:nth-child(even)').css('background-color','#fafcf8');
                        $('.rates-for-day:nth-child(odd)').css('background-color','#e4f0db');
                        $('#del-rates').css('margin-bottom', '10px');
                    } else {
                        $('.rates-for-day').css('background-color','#fff');
                    }
                    currentElement.addClass('selected').siblings().removeClass('selected');
                    this.preselectRate();
                    this.enableRadioButtonsFix();
                }.bind(this);


                this.selectMobileDeliveryDate = function (shippingGroup) {
                    this.mobileDeliveryRatesForDay(shippingGroup.selectedRates());
                }.bind(this);


                this.deliveryRatesForDay = ko.observableArray(padShippingRates({rates: []}));
                this.mobileDeliveryRatesForDay = ko.observableArray();

                this.mobileDeliveryRatesVisibility = ko.computed(function () {
                    return this.mobileDeliveryRatesForDay().length > 0;
                }.bind(this));

                var initRatesForDay = ko.computed(function () {
                    this.deliveryRatesForDay(padShippingRates({rates: []}));
                }.bind(this));

                var findEconomyRate = function () {
                    var economyRates = shippingService.getShippingRates()().filter(function (rate) {
                        return rate.method_code === 'acceptableCarrierServiceGroupCodes:ECONOMY';
                    });
                    return economyRates[0] || null;
                };

                this.economyRate = ko.computed(function () {
                    var economy = findEconomyRate();
                    return economy ? economy : dummyRate;
                });

                return this;
            },

            /** 
             * Load data from server for shipping step 
             */
            navigate: function () {
                //load data from server for shipping step
            },

            /**
             * @return {*}
             */
            getPopUp: function () {
                var self = this,
                    buttons;

                if (!popUp) {
                    buttons = this.popUpForm.options.buttons;
                    this.popUpForm.options.buttons = [
                        {
                            text: buttons.save.text ? buttons.save.text : $t('Save Address'),
                            class: buttons.save.class ? buttons.save.class : 'action primary action-save-address',
                            click: self.saveNewAddress.bind(self)
                        },
                        {
                            text: buttons.cancel.text ? buttons.cancel.text : $t('Cancel'),
                            class: buttons.cancel.class ? buttons.cancel.class : 'action secondary action-hide-popup',
                            click: function () {
                                this.closeModal();
                            }
                        }
                    ];
                    this.popUpForm.options.closed = function () {
                        self.isFormPopUpVisible(false);
                    };
                    popUp = modal(this.popUpForm.options, $(this.popUpForm.element));
                }

                return popUp;
            },

            /**
             * Show address form popup
             */
            showFormPopUp: function () {
                this.isFormPopUpVisible(true);
            },

            /**
             * Save new shipping address
             */
            saveNewAddress: function () {
                var addressData,
                    newShippingAddress;

                this.source.set('params.invalid', false);
                this.source.trigger('shippingAddress.data.validate');

                if (!this.source.get('params.invalid')) {
                    addressData = this.source.get('shippingAddress');
                    // if user clicked the checkbox, its value is true or false. Need to convert.
                    addressData.save_in_address_book = this.saveInAddressBook ? 1 : 0;

                    // New address must be selected as a shipping address
                    newShippingAddress = createShippingAddress(addressData);
                    selectShippingAddress(newShippingAddress);
                    checkoutData.setSelectedShippingAddress(newShippingAddress.getKey());
                    checkoutData.setNewCustomerShippingAddress(addressData);
                    this.getPopUp().closeModal();
                    this.isNewAddressAdded(true);
                }
            },

            /**
             * Shipping Method View
             */
            rates: shippingService.getShippingRates(),
            isLoading: shippingService.isLoading,
            isSelected: ko.computed(function () {
                    return quote.shippingMethod() ?
                    quote.shippingMethod().carrier_code + '_' + quote.shippingMethod().method_code
                        : null;
                }
            ),

            /**
             * @param {Object} shippingMethod
             * @return {Boolean}
             */
            selectShippingMethod: function (shippingMethod) {
                var carrier_method_code = shippingMethod.carrier_code + '_' + shippingMethod.method_code;
                selectShippingMethodAction(shippingMethod);
                checkoutData.setSelectedShippingRate(carrier_method_code);
                if(carrier_method_code === shippingMethod.carrier_code + '_' + "acceptableCarrierServiceGroupCodes:ECONOMY"){
                    $('.table-checkout-shipping-method select').prop('selectedIndex',0);
                }
                return true;
            },

            selectMobileShippingMethod: function (shippingMethod) {
                for(var i=0; i < shippingMethod.selectedRates().rates.length; i++) {
                    var carrier_code = shippingMethod.selectedRates().rates[i].carrier_code;
                    var method_code = shippingMethod.selectedRates().rates[i].method_code;
                    var combo = carrier_code + '_' + method_code;
                    if(combo === $("#mobile-delivery-option").val()){
                        shippingMethod = shippingMethod.selectedRates().rates[i];
                        this.selectShippingMethod(shippingMethod);
                        return true;
                    }
                }
            },

            /**
             * Set shipping information handler
             */
            setShippingInformation: function () {
                if (this.validateShippingInformation()) {
                    setShippingInformationAction().done(
                        function () {
                            stepNavigator.next();
                        }
                    );
                }
            },

            /**
             * @return {Boolean}
             */
            validateShippingInformation: function () {

                var shippingAddress,
                    addressData,
                    loginFormSelector = 'form[data-role=email-with-possible-login]',
                    emailValidationResult = customer.isLoggedIn(),
                    initialPostcode = quote.shippingAddress().postcode.replace(' ', '').toLowerCase(),
                    initialCountry = quote.shippingAddress().countryId.toLowerCase();

                if (!quote.shippingMethod()) {
                    this.errorValidationMessage($.mage.__('Please specify a shipping method.'));

                    return false;
                }

                if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }

                if (this.isFormInline) {
                    this.source.set('params.invalid', false);
                    this.source.trigger('shippingAddress.data.validate');

                    if (this.source.get('shippingAddress.custom_attributes')) {
                        this.source.trigger('shippingAddress.custom_attributes.data.validate');
                    }

                    if (this.source.get('params.invalid') || !quote.shippingMethod().method_code || !quote.shippingMethod().carrier_code || !emailValidationResult
                    ) {
                        return false;
                    }

                    shippingAddress = quote.shippingAddress();
                    addressData = addressConverter.formAddressDataToQuoteAddress(
                        this.source.get('shippingAddress')
                    );

                    //Copy form data to quote shipping address object
                    for (var field in addressData) {

                        if (addressData.hasOwnProperty(field) &&
                            shippingAddress.hasOwnProperty(field) &&
                            typeof addressData[field] != 'function' &&
                            _.isEqual(shippingAddress[field], addressData[field])
                        ) {
                            shippingAddress[field] = addressData[field];
                        } else if (typeof addressData[field] != 'function' && !_.isEqual(shippingAddress[field], addressData[field])) {
                            shippingAddress = addressData;
                            break;
                        }
                    }

                    if (customer.isLoggedIn()) {
                        shippingAddress.save_in_address_book = 1;
                    }
                    selectShippingAddress(shippingAddress);

                    var currentPostcode = shippingAddress.postcode.replace(' ', '').toLowerCase();
                    var currentCountry = shippingAddress.countryId.toLowerCase();
                }

                // If address has been altered since initial request to metapack, reselect delivery option based on new address
                if (typeof currentCountry !== 'undefined' &&  typeof currentPostcode !== 'undefined') {
                    if (initialCountry != currentCountry || initialPostcode != currentPostcode) {
                        this.onSubmit();
                        this.errorValidationMessage($.mage.__('Address details have been changed. Please re-select your delivery option.'));
                        return false;
                    }
                }

                if (!emailValidationResult) {
                    $(loginFormSelector + ' input[name=username]').focus();

                    return false;
                }

                return true;
            },

            onSubmit: function () {
                this.source.set('params.invalid', false);
                this.source.trigger('shippingAddress.data.validate');

                if (!this.source.get('params.invalid')) {
                    this.submitWasClicked(true);
                    this.isContinueToShippingMethodButtonVisible(false);

                    // the quote address is not always populated with the form field data,
                    // but the source address is.
                    var sourceAddress = this.source.get('shippingAddress');
                    var quoteAddress = quote.shippingAddress();
                    // extend the quoteAddress with the values from the properties address but in camelCase
                    Object.keys(sourceAddress).forEach(function(k) {
                        var nk = k.replace(/(\_[a-z])/g, function($1) { return $1.toUpperCase().replace('_',''); });
                        quoteAddress[nk] = sourceAddress[k];
                    });

                    // thanks to Alan Storm:
                    // create rate registry cache
                    // the two calls are required
                    // because Magento caches things
                    // differently for new and existing
                    // customers (a FFS moment):
                    rateRegistry.set(quoteAddress.getKey(), null);
                    rateRegistry.set(quoteAddress.getCacheKey(), null);

                    // with rates cleared, the observable listeners will
                    // update everything when the rates are updated:
                    quote.shippingAddress(quoteAddress);
                }
            },
            toggleNextDayMessage: function () {
                $('#toggle-next-day').slideToggle('fast');
            }
        });
    }
);
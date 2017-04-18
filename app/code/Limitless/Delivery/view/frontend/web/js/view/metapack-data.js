define([], function() {
    'use strict';

    // To do list:
    //
    // remove hardcoded ECONOMY economy rate, take from config
    // enable radio buttons
    // add good css classes to make styling easy
    // add style

    function getDeliverySlotFromRate(shippingRate) {
        var match = shippingRate.method_code.match(/acceptableDeliverySlots:([^T]+)T/);
        return match === null ? '' : match[1];
    }

    var hasDateInMethodCode = function (shippingRate) {
        var slot = getDeliverySlotFromRate(shippingRate);
        return !isNaN(new Date(slot).getTime());
    };

    var addDate = function(shippingRate){
        shippingRate.date = getDeliverySlotFromRate(shippingRate);
        return shippingRate;
    };

    var addDaysFromToday = function(shippingRate){
        var deliveryDate = new Date(shippingRate.date);
        var today = new Date(new Date().toISOString().substr(0,10));
        shippingRate.daysFromToday = Math.floor((deliveryDate.getTime() - today.getTime()) / (24 * 60 * 60000));
        return shippingRate;
    };

    var groupByDays = function (shippingRates) {
        var result = [];
        shippingRates.forEach(function (rate, index) {
            if (typeof result[rate.daysFromToday] === 'undefined') {
                result[rate.daysFromToday] = [];
            }
            result[rate.daysFromToday].push(rate);
        });
        return result;
    };

    var philGaps = function (groupedShippingRates) {
        var daysFromToday = Object.keys(groupedShippingRates);
        var firstDay = 1;
        var lastDay = daysFromToday[daysFromToday.length -1];
        lastDay = Math.ceil(lastDay / 7) * 7;

        for (var day = firstDay; day <= lastDay; day++) {
            if (typeof groupedShippingRates[day]==='undefined') {
                groupedShippingRates[day]=[];
            }
        }
        return groupedShippingRates;
    };

    var moveRatesIntoSubArray = function (group) {
        return {'rates' : group};
    };

    var addFromPriceToGroup = function (group) {
        var min = group.rates.reduce(function(price, rate){
            return ((price === -1) || (rate.amount < price)) ? rate.amount : price;
        },-1);

        group.from_price = Math.max(min,0);
        return group;
    };

    var addDateToGroup = function (group,daysFromToday) {
        group.date = new Date(new Date().getTime() + daysFromToday * (24 * 60 * 60000));
        return group;
    };

    return {
        getMaxOptionsPerDay: function(shippingRates){
            return this.getDeliveryOptions(shippingRates).reduce(function(acc, deliveryGroup){
                return Math.max(acc, deliveryGroup.rates.length);
            },0);
        },

        getDeliveryOptions: function(shippingRates) {
            var modifiedRates = shippingRates.filter(hasDateInMethodCode).map(addDate).map(addDaysFromToday);
            var groupedRates = philGaps(groupByDays(modifiedRates));

            return groupedRates.map(moveRatesIntoSubArray).map(addFromPriceToGroup).map(addDateToGroup);
        }
    };
});

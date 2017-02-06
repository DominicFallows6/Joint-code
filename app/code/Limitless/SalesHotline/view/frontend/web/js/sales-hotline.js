define(['jquery'], function($) {

    "use strict";

    var toggleSalesHotline = function() {

        var openingHoursPopUp = $('#opening-times-container');

        $("a.show-sales-hotline").click(function() {
            $(openingHoursPopUp).toggle();
            return false;
        });

        $(".close").click(function() {
            $(openingHoursPopUp).hide();
        });

        $(document).click(function() {
            $(openingHoursPopUp).hide();
        });

        $(openingHoursPopUp).click(function(e) {
            e.stopPropagation();
        });
    };

    /**
     * @param data.openingTimes[day]
     * @param data.closedValue[day]
     * @param data.checkInterval
     * @param data.salesNumber
     * @param data.timeZone
     * @param data.openText
     * @param data.closedText
     * @param data.utcOffset
     * @param data.helpCentreLink
     */

    return function (data) {

        function getOpeningTimes() {

            var d = getDateObject();
            var day = d.getDay();
            var hours = String(d.getHours()).padLeft('00');
            var minutes = String(d.getMinutes()).padLeft('00');
            var seconds = String(d.getSeconds()).padLeft('00');
            var date = d.getFullYear() + '-' + String(d.getMonth() + 1).padLeft('00') + '-' + String(d.getDate()).padLeft('00');
            var salesNumberDiv = $('#opening-times');
            var currentOpeningTimes = data.openingTimes[day];
            var closed = data.closedValue[day];
            var openText = data.openText;
            var closedText = data.closedText;
            var timeZone = data.timeZone;
            var salesNumber = data.salesNumber;
            var helpCentreLink = data.helpCentreLink;
            var openClosedTime = currentOpeningTimes.split("-");
            var currentTime = hours + ':' + minutes;
            var openTime = openClosedTime[0].trim();
            var closedTime = openClosedTime[1].trim();
            var checkOpeningTimesFrequency = (data.checkInterval * 1) * 1000;

            if(currentTime >= openTime && currentTime < closedTime && closed === "No") {
                $(salesNumberDiv).html('<div class="open">' + openText + ' ' + '<span class="opening-hours">' + currentOpeningTimes + ' ' + timeZone + '</span>' + '</div>' + salesNumber + ' ' + helpCentreLink);
            } else {
                $(salesNumberDiv).html('<div class="closed">' + closedText + '</div> ' + helpCentreLink);
            }

            if(checkOpeningTimesFrequency > 0) {
                setTimeout(getOpeningTimes, checkOpeningTimesFrequency);
            }
        }

        String.prototype.padLeft = function (padStr) {
            return String(padStr + this).slice(-padStr.length);
        };


        function getDateObject() {

            var dateObject = new Date();
            var localTimeOffset = dateObject.getTimezoneOffset() * 60;
            dateObject.setSeconds(dateObject.getSeconds() + localTimeOffset + parseInt(data.utcOffset));
            return dateObject;

        }

        getOpeningTimes();

        toggleSalesHotline();

    };


});
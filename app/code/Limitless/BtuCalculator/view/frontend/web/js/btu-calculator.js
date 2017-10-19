define(["jquery","jquery/validate"], function($) {

    "use strict";

    return function(data) {

        var btu = $('input[name="btu_total"]');
        var watts = $('input[name="watts_total"]');

        function buildFilteredLink() {

            var suitableProductsLink = $('.suitable-products > a');
            var wattsRequired;
            var filteredLabel = data.filter_output_label;
            var filteredValue = data.filter_output_value;
            var filteredUrl = data.filter_output_url;
            var rangeArrayTo = [];
            var rangeTo = null;
            var filterIdKey;
            var filteredId;

            if (data.btu_output == 1) {
                wattsRequired = parseInt($(btu).val().replace(/,/g, ''))
            } else {
                wattsRequired = parseInt($(watts).val().replace(/,/g, ''));
            }

            /* Split the filtered label range into separate array to get range to values */
            for (var i=0; i < filteredLabel.length; i++) {
                var split = filteredLabel[i].split('-');
                rangeArrayTo.push(split[1]);
            }

            /* return the closest value up when comparing the BTU result against the rangeArrayFrom array */
            $.each(rangeArrayTo, function() {
                if (this >= wattsRequired && (rangeTo == null || (wattsRequired - this) > (wattsRequired - rangeTo))) {
                    rangeTo = this;
                }
            });

            /* Get the array key for the filterId */
            for (var n in rangeArrayTo) {
                if (rangeArrayTo.hasOwnProperty(n)) {
                    if (rangeArrayTo[n] === rangeTo) {
                        filterIdKey = n;
                    }
                }
            }

            /* Get filter id value from the key returned above */
            for (var j=0; j < filteredValue.length; j++) {
                if (filteredValue[filterIdKey] != null){
                    filteredId = filteredValue[filterIdKey];
                } else {
                    filteredId = "no_results";
                }

            }

            $(suitableProductsLink).attr("href", filteredUrl+'='+filteredId);

        }

        function showBtuResults() {

            var btuTotal;
            var wattsTotal;

            $('html,body').animate({scrollTop: $(".scrolldown").offset().top}, 'slow');

            $('.calculated').show();

            window.randomize = function () {
                $('.radial-progress').attr('data-progress', 100);
            };

            setTimeout(window.randomize);

            if (data.btu_output == 1) {
                btuTotal = btu.val();
                wattsTotal = watts.val();
            } else {
                btuTotal = watts.val();
                wattsTotal = watts.val();
            }

            $({countNum: 0}).animate({countNum: btuTotal}, {
                duration: 1000,
                easing: 'linear',
                step: function () {
                    btu.val(Math.floor(this.countNum));
                },
                complete: function () {
                    btu.val(btuTotal);
                }
            });

            $({countNum: 0}).animate({countNum: wattsTotal}, {
                duration: 1000,
                easing: 'linear',
                step: function () {
                    watts.val(Math.floor(this.countNum));
                    if (data.btu_output != 1) {
                        watts.hide();
                    }
                },
                complete: function () {
                    watts.val(wattsTotal);
                    buildFilteredLink();
                }
            });
        }

        function updateRoom() {
            var answer;
            var glassloss = 0;
            var wall1 = 0;
            var wall2 = 0;
            var wall3 = 0;
            var ceiling = 0;
            var floored = 0;
            var air = 0;
            var uvalue = 0;
            var wallcount = parseInt(document.btuForm.walls.value, 10);
            var floorsize = 0;
            var category = '';

            var height = parseFloat(document.btuForm.height.value.replace(',', '.'));
            var width = parseFloat(document.btuForm.width.value.replace(',', '.'));
            var depth = parseFloat(document.btuForm.depth.value.replace(',', '.'));
            var windarea = parseFloat(document.btuForm.windarea.value.replace(',', '.'));

            if (document.btuForm.units[1].checked) {
                height *= 0.3048;
                width *= 0.3048;
                depth *= 0.3048;
                windarea *= 0.3048;
            }

            floorsize = width * depth;
            glassloss = windarea * parseFloat(document.btuForm.windtype.value);
            wall1 = height * width;
            wall1 -= windarea;
            wall1 *= parseFloat(document.btuForm.walltype.value);

            if (wallcount > 1) {
                wall2 = height * depth;
                wall2 *= parseFloat(document.btuForm.walltype.value);
            }
            if (wallcount > 2) {
                wall3 = height * width;
                wall3 *= parseFloat(document.btuForm.walltype.value);
            }
            ceiling = floorsize * parseFloat(document.btuForm.aboveroom.value);
            var belowroom = parseInt(document.btuForm.belowroom.value, 10);
            if (belowroom == 1) floored = 0;
            else if (belowroom == 2 && floorsize > 16) floored = 0.96 * floorsize;
            else if (belowroom == 2 && floorsize <= 16) floored = 1.27 * floorsize;
            else if (belowroom == 3 && floorsize > 16 && wallcount == 1) floored = 0.45 * floorsize;
            else if (belowroom == 3 && floorsize > 16 && wallcount > 1) floored = 0.72 * floorsize;
            else if (belowroom == 3 && floorsize <= 16 && wallcount == 1) floored = 0.74 * floorsize;
            else if (belowroom == 3 && floorsize <= 16 && wallcount > 1) floored = 1.22 * floorsize;
            else if (belowroom == 4) floored = 2.13 * floorsize;
            air = floorsize * height * 0.33;
            switch (document.btuForm.roomtype.value) {
                case "17":
                    air *= 1.5;
                    break;
                case "19":
                    air *= 0.5;
                    break;
                case "22":
                    air *= 1;
                    break;
                case "23":
                    air *= 2.0;
                    break;
            }
            answer = glassloss + wall1 + wall2 + wall3 + ceiling + floored + air;
            answer *= parseFloat(document.btuForm.roomtype.value);
            if (belowroom == 1) answer -= floorsize * 1.6 * 3;
            if (isNaN(answer)) {
                document.btuForm.watts_total.value = "";
                document.btuForm.btu_total.value = "";

            }
            else {
                answer *= 1.15;
                document.btuForm.watts_total.value = Math.floor(answer);
                document.btuForm.btu_total.value = Math.floor(answer * 3.412);
            }

            showBtuResults();

        }

        function calculateBtu() {

            // Allow commas as well as dots for number validation
            $.extend($.validator.methods, {
                number: function(value, element) {
                    return this.optional(element)
                        || /^-?(?:\d+|\d{1,3}(?:\.\d{3})+)(?:[,.]\d+)?$/.test(value);
                }
            });

            $("#btuForm").validate({
                rules: {
                    width: {
                        required: true,
                        number: true
                    },
                    height: {
                        required: true,
                        number: true
                    },
                    depth: {
                        required: true,
                        number: true
                    },
                    windarea: {
                        required: true,
                        number: true
                    },
                    roomtype: "required",
                    belowroom: "required",
                    aboveroom: "required",
                    walltype: "required",
                    windtype: "required",
                    walls: "required"
                },
                errorPlacement: function(error, element) {},
                submitHandler: function(){
                    updateRoom();
                }
            });
        }

        calculateBtu();

        $(".reset-btu").click(function() {
            $("#btuForm").trigger("reset");
            $('.radial-progress').attr('data-progress', 0);
            $('.calculated').hide();
        });

        $('#measurement input').click(function() {
            $(this).parent().parent().addClass("current");
            $(this).parent().parent().siblings().removeClass("current");
        });

        $('.dropdown select').change(function(){
            if ($(this).children('option:first-child').is(':selected')) {
                $(this).addClass('option-grey');
            } else {
                $(this).removeClass('option-grey');
            }
        });
    }
});
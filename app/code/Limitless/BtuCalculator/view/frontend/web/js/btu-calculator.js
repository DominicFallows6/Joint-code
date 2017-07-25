define(["jquery","jquery/validate"], function($) {

    "use strict";

    return function() {

        function addCommas(nStr) {
            nStr += '';
            var x = nStr.split('.');
            var x1 = x[0];
            var x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            }
            return x1 + x2;
        }

        function showBtuResults() {

            var btu = $('input[name="subtotalal2"]');
            var watts = $('input[name="subtotalal"]');

            $('html,body').animate({scrollTop: $(".scrolldown").offset().top}, 'slow');

            $('.calculated').show();

            window.randomize = function () {
                $('.radial-progress').attr('data-progress', 100);
            };

            setTimeout(window.randomize);

            $({countNum: 0}).animate({countNum: btu.val()}, {
                duration: 1000,
                easing: 'linear',
                step: function () {
                    btu.val(Math.floor(this.countNum));
                },
                complete: function () {
                    var btuTotal = btu.val();
                    var commas = addCommas(btuTotal);
                    btu.val(commas);
                }
            });

            $({countNum: 0}).animate({countNum: watts.val()}, {
                duration: 1000,
                easing: 'linear',
                step: function () {
                    watts.val(Math.floor(this.countNum));
                },
                complete: function () {
                    var wattsTotal = watts.val();
                    var commas = addCommas(wattsTotal);
                    watts.val(commas);
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

            var height = parseFloat(document.btuForm.height.value);
            var width = parseFloat(document.btuForm.width.value);
            var depth = parseFloat(document.btuForm.depth.value);
            var windarea = parseFloat(document.btuForm.windarea.value);

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
                document.btuForm.subtotalal.value = "";
                document.btuForm.subtotalal2.value = "";

            }
            else {
                answer *= 1.15;
                document.btuForm.subtotalal.value = Math.floor(answer);
                document.btuForm.subtotalal2.value = Math.floor(answer * 3.412);
            }

            showBtuResults();

        }

        function calculateBtu() {

            $("#btuForm").validate({
                rules: {
                    width: {
                        required: true,
                        integer: true
                    },
                    height: {
                        required: true,
                        integer: true
                    },
                    depth: {
                        required: true,
                        integer: true
                    },
                    windarea: {
                        required: true,
                        integer: true
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
define(['jquery','moment','owlcarousel'], function ($, moment) {

    "use strict";

    return function (config) {

        var trustPilotFooter = "#trustpilot-footer";
        var trustPilotCatalog = "#trustpilot-catalog";
        var trustPilotProduct = "#trustpilot-product";
        var trustPilotPagination = ".tp-pagination";
        var trustPilotStarLabels = config.label_ratings.split(',');

        function truncateText(string, length) {
            return string.length > length ? string.substr(0, length) + '...' : string;
        }

        function reviewsPagination() {
            $(trustPilotPagination + ' > a:gt(5)').remove();
            $(trustPilotCatalog + ' .tp-latest-reviews > div:first-child').addClass('active');
            $(trustPilotPagination +' > a:first-child').addClass('active');
            $(trustPilotPagination +' > a').click(function(e) {
                 e.preventDefault();
                 var reviewId = $(this).attr('id');
                 $(trustPilotPagination +' > a').removeClass('active');
                 $(this).addClass('active');
                 $('.tp-latest-reviews > div').removeClass('active');
                 $(this).parent().prev('.tp-latest-reviews').children('#'+reviewId).addClass('active');
            });
        }

        function callSlider() {
            $(trustPilotProduct+ ' .owl-carousel').owlCarousel({
                loop:true,
                margin:40,
                nav:true,
                dots: false,
                responsiveClass:true,
                navText: ["",""],
                responsive:{
                    480: {
                        items:1
                    },
                    768:{
                        items:2
                    },
                    1000:{
                        items:3
                    },
                    1280:{
                        items:4
                    }
                }
            });
        }

        function setTPBusinessData(result) {

            var trustPilotStarLabelScores = '';
            if (typeof trustPilotStarLabels[result.stars-1] !== 'undefined') {
                trustPilotStarLabelScores = trustPilotStarLabels[result.stars - 1];
            }

            $(trustPilotFooter + " .tp-rating").append(result.trustScore);
            $(trustPilotFooter + " .tp-number-of-reviews").append(result.numberOfReviews.total);
            $(trustPilotFooter + " .tp-stars").addClass('star-'+result.stars);
            $(trustPilotFooter + " .tp-trust-score").append(trustPilotStarLabelScores);
            if($(trustPilotCatalog).length) {
                $(trustPilotCatalog + " #trust-score-value").append(result.trustScore);
                $(trustPilotCatalog + " #tp-number-of-reviews").append(result.numberOfReviews.total);
                $(trustPilotCatalog + " .tp-stars").addClass('star-'+result.stars);
                $(trustPilotCatalog + " #tp-business-link").attr("href",config.business_base_url + '/review/' + config.trustpilot_sitename);
            }
            if($(trustPilotProduct).length) {
                $(trustPilotProduct + " .tp-trust-score").append(trustPilotStarLabelScores);
                $(trustPilotProduct + " #tp-business-info .tp-stars").addClass('star-'+result.stars);
                $(trustPilotProduct + " #tp-number-of-reviews").append(result.numberOfReviews.total);
                $(trustPilotProduct + " #tp-business-info").attr("href",config.business_base_url + '/review/' + config.trustpilot_sitename);
            }
        }

        function setTPReviewData(result) {
            var reviews = result.reviews;
            var count = 0;
            for(var value in reviews) {
                if(reviews.hasOwnProperty(value)) {
                    ++count;
                    var reviewsTextTrimmed = reviews[value].text;
                    var reviewCreatedDate = moment(reviews[value].createdAt);
                    var lastUpdated = moment(reviewCreatedDate).format('DD/MM/YYYY');
                    $('.tp-latest-reviews').append('<div id="review-'+count+'" class="review">' +
                        '<a href="'+config.business_base_url+'/reviews/'+reviews[value].id+'" target="_blank">' +
                        '<div class="tp-stars star-'+reviews[value].stars+'">' +
                        '<div class="star-1">' +
                        '<span class="star-small"></span>' +
                        '</div>' +
                        '<div class="star-2">' +
                        '<span class="star-small"></span>' +
                        '</div>' +
                        '<div class="star-3">' +
                        '<span class="star-small"></span>' +
                        '</div>' +
                        '<div class="star-4">' +
                        '<span class="star-small"></span>' +
                        '</div>' +
                        '<div class="star-5">' +
                        '<span class="star-small"></span>' +
                        '</div>' +
                        '</div>' +
                        '<span class="created">'+lastUpdated+'</span>' +
                        '<span class="title">'+reviews[value].title+'</span>' +
                        '<span class="text">'+truncateText(reviewsTextTrimmed,100)+'</span>' +
                        '<span class="reviewer">'+reviews[value].consumer.displayName+'</span>' +
                        '</a>' +
                        '</div>');
                    $(trustPilotPagination).append('<a href="#" id="review-'+count+'" class="review"></a>');
                    reviewsPagination();
                }
            }
            callSlider();
        }

        function getTrustPilotData() {

            //Business TP

            var cacheBusinessAjax = false;
            var cacheBusinessInfo = config.cached_business_info;

            if (config.trustpilot_mode == 'cache' && cacheBusinessInfo.length > 1) {
                cacheBusinessInfo = cacheBusinessInfo.replace(/&quot;/g, '"');

                var result = JSON.parse(cacheBusinessInfo);

                if (result.fault) {
                    cacheBusinessAjax = true;
                } else {
                    setTPBusinessData(result);
                    console.log("Business Data - via cache");
                }
            } else {
                cacheBusinessAjax = true;
            }

            if (cacheBusinessAjax) {
                $.ajax({
                    url: config.business_api_url,
                    headers: {
                        'apikey': '"'+config.api_key+'"'
                    },
                    type: 'GET',
                    dataType: 'json',
                    contentType: 'application/json; charset=utf-8',
                    success: function (result) {

                        setTPBusinessData(result);
                        console.log("Business Data - via ajax");

                    },
                    error: function (error) {
                        console.log(error);
                        console.log('Unable to get business unit info');
                    }
                });
            }

            //Reviews TP

            var cacheReviewAjax = false;
            var cacheReviewInfo = config.cached_review_info;

            if (config.trustpilot_mode == 'cache' && cacheReviewInfo.length > 1) {
                cacheReviewInfo = cacheReviewInfo.replace(/&quot;/g, '"');

                result = JSON.parse(cacheReviewInfo);

                if (result.fault) {
                    cacheReviewAjax = true;
                } else {
                    setTPReviewData(result);
                    console.log("Review Data - via cache");
                }
            } else {
                cacheReviewAjax = true;
            }

            if (cacheReviewAjax) {
                $.ajax({
                    url: config.review_api_url,
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'apikey': '"'+config.api_key+'"'
                    },
                    contentType: 'application/json; charset=utf-8',
                    success: function (result) {

                        setTPReviewData(result);
                        console.log("Review Data - via ajax");
                    },
                    error: function (error) {
                        console.log(error);
                        console.log('Unable to get TP reviews');
                    }
                });
            }
        }

        getTrustPilotData();
    }
});

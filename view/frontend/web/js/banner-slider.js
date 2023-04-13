define([
    'jquery',
    'slick'
], function ($) {
    'use strict';
    return function (config) {
        $('.banner-slider').slick(
            {
                infinite: true,
                slidesToShow: 1,
                slidesToScroll: 1,
                dots: true,
                autoplay: parseInt(config.autoplay),
                autoplaySpeed: parseInt(config.autoplay_speed),
                pauseOnHover:true,
                arrows: true
            }
        );
    }
});

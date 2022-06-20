$(document).ready(function() {
    function x() {
        var delay = 1000;
        var li_count = $('.carousel-first-slider.active .banner-point > li').length -1;
        $('.carousel-first-slider.active .banner-point > li').each(function (i) {
            $(this).hide().delay(delay).fadeIn(1850).addClass('text-bigger').delay(100).queue(function (next) {
                $(this).removeClass('text-bigger');
                if (li_count == i) x();
                next();
            });
            delay += 2000;
        });
    }
    // x();

    $('#carousel_accueil').carousel({
        interval: 15000
    });
    $('#carousel_accueil').bind('slide.bs.carousel', function (e) {
        console.log('slide event!');
    });

    $('#carousel_temoignage').carousel({
        interval: false
    });

    $('.slick-demo').slick({
        infinite: false,
        speed: 300,
        slidesToShow: 4,
        slidesToScroll: 1,
        prevArrow:"<div class='button-prev-triangle slick-prev'></div>",
        nextArrow:"<div class='button-next-triangle slick-next'></div>",
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    infinite: true
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });
});
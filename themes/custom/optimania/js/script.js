$(document).ready(function() {
    $('nav input[type="checkbox"]').on('change', function() {
        $('.main-nav input[type="checkbox"]').not(this).prop('checked', false);
        $('.right-nav input[type="checkbox"]').not(this).prop('checked', false);
    });
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
        prevArrow:"<button class=\"carousel-control-prev carousel-control-prev-custom control-custom\" type=\"button\" >" +
        "          <i class=\"fa fa-caret-left button-prev-triangle\"></i>\n" +
        "        </button>",
        nextArrow:"<button class=\"carousel-control-next carousel-control-next-custom control-custom\" type=\"button\">" +
        "          <i class=\"fa fa-caret-right button-next-triangle\"></i>\n" +
        "        </button>",
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
$(document).ready(function() {
    $(".core-menu li").hover(
        function(){
            //i used the parent ul to show submenu
            $(this).children('ul').slideDown('fast');
        },
        //when the cursor away
        function () {
            $('ul', this).slideUp('fast');
        });
    //this feature only show on 600px device width
    $(".hamburger-menu").click(function(){
        $(".burger-1, .burger-2, .burger-3").toggleClass("open");
        $(".core-menu").slideToggle("fast");
    });
    $(".btn-nav").on("click", function() {
        $(".nav-content").toggleClass("showNav hideNav").removeClass("hidden");
        $(this).toggleClass("animated");
    });
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
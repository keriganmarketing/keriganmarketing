require("babel-polyfill");

window.jQuery = window.$ = require('jquery');

import tether from 'tether';

global.Tether = tether;

require('bootstrap');

window.isScrolledIntoView = function (elem) {
    let docViewTop = $(window).scrollTop();
    let docViewBottom = docViewTop + $(window).height();
    let elemTop = $(elem).offset().top;
    let elemBottom = elemTop + $(elem).height();
    return ((elemBottom >= docViewTop) && (elemTop <= docViewBottom) && (elemBottom <= docViewBottom));
};

window.loadVideo = function () {
    let windowsize = $(window).width();
    let hasTouch = 'ontouchstart' in window;
    let videoContent = '<div id="big-video-wrap">' +
        '<div id="big-video-vid">' +
        '<video width="100%" height="auto" autoplay loop muted >' +
        '<source src="/wp-content/themes/kma/vid/kma3.mp4" type="video/mp4" >' +
        '</video>' +
        '</div>' +
        '</div>';
    $('#video-container').html(videoContent);
    window.setTimeout(function () {
        $('.mast-content').addClass('reveal')
    }, 500);
};

window.rotateTestimonials = function () {
    (function () {
        let quotes = $(".quotes");
        let quoteIndex = -1;

        function showNextQuote() {
            ++quoteIndex;
            quotes.eq(quoteIndex % quotes.length)
                .fadeIn(0)
                .delay(5000)
                .fadeOut(0, showNextQuote);
        }

        showNextQuote();
    })();
};

window.handleStickyFooter = function () {
    $(window).scroll(function () {
        if (isScrolledIntoView($('#mid')) || isScrolledIntoView($('#continue-section'))) {
            $("#bot").addClass("stick").removeClass("unstick");
            $("#bot-bot").addClass("stick").removeClass("unstick");
        } else {
            $("#bot").addClass("unstick").removeClass("stick");
            $("#bot-bot").addClass("unstick").removeClass("stick");
        }
    });
};

window.handleClientPage = function () {
    if (isScrolledIntoView($('#hider'))) {
        $("#scrollbg").removeClass("show").addClass("hide");
    } else {
        $("#scrollbg").addClass("show").removeClass("hide");
    }

    $(window).scroll(function () {
        if ($(window).scrollTop() == 0) {
            $("#scrollbg").removeClass("show").addClass("hide");
        }
        if ($(window).scrollTop() > 0) {
            $("#scrollbg").addClass("show").removeClass("hide");
        }
    });
};

window.handleHomeHeader = function () {

    $(window).scroll(function () {

        if ($(window).scrollTop() == 0) {
            $("#scrollbg").removeClass("show").addClass("hide");
        }
        if ($(window).scrollTop() > 0) {
            $("#scrollbg").addClass("show").removeClass("hide");
        }
    });

};

window.handleClickDown = function () {
    $('#clickdown a').on('click', function (e) {
        e.preventDefault();
        var target = $(this).attr('href');
        $('html, body').stop().animate({
            scrollTop: $(target).offset().top
        }, 1000);
    });
};

window.handleTeam = function () {
    $(".grayscale").hover(
        function () {
            $(this).addClass('grayscale-off');
        }, function () {
            $(".grayscale").gray();
        }
    );
    $(function () {
        $(".grayscale").gray();
    });
}

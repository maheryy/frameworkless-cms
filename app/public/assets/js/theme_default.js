/*
//Sticky Header
$(window).scroll(function () {
    if ($(window).scrollTop() > 100) {
        $('.main-header').addClass('sticky');
    } else {
        $('.main-header').removeClass('sticky');
    }
});
*/

function setHero(urlImage) {
    $('.hero-header').css('background-image', `url('${urlImage}')`);
    $('.hero-header').css('opacity', '1');
}


$(document).ready(function () {
    setHero($('#hero-img').data('url'));
});

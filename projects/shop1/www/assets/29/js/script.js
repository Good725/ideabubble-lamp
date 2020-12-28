$(document).ready(function() {
    /*------------------------------------*\
     #Header links
     \*------------------------------------*/

    /* Generates custom menu button*/
    var wrapper = $('<div>').addClass('header-links-wrapper');
    var button = $('<button>').addClass('header-links-collapse').attr('id', 'header-links-collapse').append($('<span>').addClass('flaticon-bars'));
    var list = $('<ul>').addClass('header-links').attr('id', 'header-links').append($('<a>').attr('href', '/').html('View Mobile Website'));
    wrapper.append(button);
    wrapper.append(list);

    $('.user-tools-wrapper').before(wrapper);


    $('#header-links-collapse').on('click', function () {
        var $header_links = $('#header-links');

        if ($header_links.hasClass('header-links--visible')) {
            $header_links.removeClass('header-links--visible');
        }
        else {
            $header_links.addClass('header-links--visible');
        }
    });

});
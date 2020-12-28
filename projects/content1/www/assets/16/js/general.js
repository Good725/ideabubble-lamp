$(document).ready(function ()
{
    // move_sidebar();

    $('#submit-quick-contact').click(function (ev)
    {
        ev.preventDefault();
        var form = $('#form-quick-contact');
        var captcha = form.find('#recaptcha_response_field');

        var valid = (form.validationEngine('validate'));
        var captcha_valid = (captcha.val() != '' || captcha === 'undefined');

        if (valid && captcha_valid) {
            form.attr('action', '/frontend/formprocessor').submit();
        }
        else {
            setTimeout('removeBubbles()', 5000);
        }

    });

    $("#submit-newsletter").click(function (ev) {
        ev.preventDefault();
        var valid = ($("#form-newsletter--").validationEngine('validate'));
        if (valid) {
            $('#form-newsletter--').attr('action', '/frontend/formprocessor').submit();
        }
        else {
            setTimeout('removeBubbles()', 5000);
        }

    });

    /* Custom audio player
     * adapted from http://www.theparticlelab.com/building-a-custom-html5-audio-player-with-jquery/
     */
    $('audio.player').removeAttr('controls').wrap('<div class="audio_player"></div>');

    $('.audio_player').prepend('' +
        '<span class="play_toggle"></span>' +
        '<span class="gutter">' +
            '<span class="loading"></span>' +
            '<input type="range" min="0" max="100" value="0" />' +
        '</span>' +
        '<span class="time_remaining"></span>');

    $('.audio_player audio').each(function()
    {
        var audio             = this;
        var player            = $(this).parent();
        var loadingIndicator  = player.find('.loading');
        var positionIndicator = player.find('input');
        var timeleft          = player.find('.time_remaining');

        if ((audio.buffered != undefined) && (audio.buffered.length != 0))
        {
            $(audio).bind('progress', function()
            {
                var loaded = parseInt(((audio.buffered.end(0) / audio.duration) * 100), 10);
                loadingIndicator.css({width: loaded + '%'});
            });
        }
        else
        {
            loadingIndicator.remove();
        }

        $(audio).bind('timeupdate', function()
        {

            var rem  = parseInt(audio.duration - audio.currentTime, 10),
                pos  = (audio.currentTime / audio.duration) * 100,
                mins = Math.floor(rem/60,10),
                secs = rem - mins*60;

            positionIndicator.val(pos);
            timeleft.text('-' + mins + ':' + (secs > 9 ? secs : '0' + secs));
        });

        $(audio)
            .bind('play',function()
            {
                player.find(".play_toggle").addClass('playing');
            })
            .bind('pause ended', function()
            {
                player.find(".play_toggle").removeClass('playing');
            });

        player.find(".play_toggle").click(function()
        {
            (audio.paused) ? audio.play() : audio.pause();
        });
    });


});

$(window).resize(function()
{
     // move_sidebar();
});


function move_sidebar()
{
    var window_width = $(window).width();
    if (window_width < 988)
    {
        $('#banner_wrapper').after($('#column-242'));
    }
    else
    {
        $('#content_area').after($('#column-242'));
    }
}
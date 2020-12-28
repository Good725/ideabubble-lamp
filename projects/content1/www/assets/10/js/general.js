$(document).ready(function ()
{
    // move_sidebar();
	var $enquiry_form = $('#enqury_form');

    $('#submit-quick-contact').click(function (ev)
    {
        ev.preventDefault();
        var form = $('#form-quick-contact');
        var captcha = form.find('#recaptcha_response_field');

        var valid = (form.validationEngine('validate'));

		if (valid && captcha === 'undefined')
		{
			// If validation passes and CAPTCHA has not bee set up, submit the form
			form.attr('action', '/frontend/formprocessor').submit();
		}
		else if (valid)
		{
			var $captcha_div = $('#enqury_form').find('#recaptcha_widget_div');
			// If validation passes and CAPTCHA has been set up, display the CAPTCHA
			if ( ! $captcha_div.find('[type="submit"]').length)
			{
				$('#recaptcha_widget_div').find('#recaptcha_area').append('<button type="submit">Submit</button>');
			}
			$captcha_div.show();
		}
		else {
			setTimeout('removeBubbles()', 5000);
		}
    });

	// When the enquiry form CAPTCHA is filled out, submit the form
	$enquiry_form.find('#recaptcha_response_field').on('change', function()
	{
		if (this.value.trim() != '')
		{
			$('#enqury_form').find('#form-quick-contact').attr('action', '/frontend/formprocessor').submit();
		}
	});

	// Dismiss the modal when clicked away from
	$enquiry_form.find('#recaptcha_widget_div').on('click', function(ev)
	{
		if (ev.target == this)
		{
			$(this).fadeOut('fast', function()
			{
				$(this).css('display', '');
			});
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

	$('[action="frontend/formprocessor/"], [action="frontend/formprocessor"]').on('submit', function(ev)
	{
		ev.preventDefault();
		if ($(this).validationEngine('validate'))
		{
			this.submit();
		}
		else
		{
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
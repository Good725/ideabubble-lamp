$(document).ready(function()
{
    card_builder_logo             = new Image();
    card_builder_logo.crossOrigin = 'Anonymous';
    card_builder_logo.onload      = function() { draw_card(); };
    card_builder_logo.src         = document.getElementById('cb-logo').src;

    draw_card();
    $('.update_card').on('change keyup', function()
    {
        draw_card();
    });
});

$('.order_card_button').on('click', function(ev)
{
    ev.preventDefault();
    document.getElementById('cb-redirect').value = this.getAttribute('data-redirect');
    document.getElementById('card_builder_form').submit();
});

function draw_card()
{
    var canvas      = document.getElementById('card_builder_canvas');
    var context     = canvas.getContext('2d');

    // 300 dpi for the print ready image.
    // 170 when viewing the card on site. The number is of no semantic meaning. It's just enough to make the card fill the supplied area.
    var dpi   = (document.getElementById('orders_table')) ? 300 : 170;
    var dpmm  = dpi / 25.4; // dots per mm
    var dppt  = dpi / 72;   // dots per point
    /*
     * Multiply by dpi to convert inch measurements to pixels
     * Multiply by dmpmm to convert mm measurements to pixels
     * Multiply by dppt to convert point measurements to pixels
     */

    canvas.width    = 89.5 * dpmm;
    canvas.height   = 50.8 * dpmm;
    var main_color  = '#000000';
    var highlight   = '#5268c2';
    // $(canvas).css('width', '60.1%');

    // Reset
    canvas.width      = 0 + canvas.width;
    context.fillStyle = '#ffffff';
    context.fillRect(0, 0, canvas.width, canvas.height);

    /*
     * Address
     */
    var office        = $('#cb-office').find(':selected');
    var address       = office.data('address') ? office.data('address').split('\n') : [];
    var line_height   = 10.25 * dppt;
    var line          = (4 * dpmm) / line_height;
    var font_size     = 8.25 * dppt;
    context.font      = '300 '+(font_size)+'px "HelveticaNeue-Light", "Helvetica Neue", Helvetica, Arial, sans-serif';
    context.fillStyle = main_color;

    // Get length in px of longest address line
    var address_width, max = 0;
    for (var i = 0; i < address.length; i++)
    {
        address_width = (context.measureText(address[i].trim())).width;
        max = (address_width > max) ? address_width : max;
    }

    // draw all address lines starting at the length of the longest line + margin from the right-hand side
    var indent = canvas.width - (4 * dpmm) - max;
    for (i = 0; i < address.length; i++)
    {
        // "+ font_size * .75" so it measures to the bottom of the letter, including descender (context.textBaseline = 'hanging' renders differently across browsers)
        context.fillText(address[i].trim(), indent, line * line_height + font_size *.75);
        line++;
    }

    /*
     * Logo
     */
    try
    {
        context.drawImage(card_builder_logo, 4 * dpmm, 4 * dpmm, 33 * dpmm, 7 * dpmm);
    }
    catch(e)
    {

        /* Fallback. Mainly for old browsers. Use text instead of the SVG */
        // Business name
        font_size            = 5 * dpmm;
        context.font         = 'bold italic '+(font_size)+'px "Helvetica Neue", Helvetica, Arial, sans-serif';
        context.fillStyle    = highlight;
        // "+ font_size * .75" so it measures to the bottom of the letter, including descender (context.textBaseline = 'hanging' renders differently across browsers)
        context.fillText('REGENERON', 4 * dpmm, 4 * dpmm + font_size *.75);

        // Country subtitle
        context.font       = '300 '+(2.36*dpmm)+'px "HelveticaNeue-Light", "Helvetica Neue", Helvetica, Arial, sans-serif';
        context.fillStyle  = '#666';
        context.fillText(' I  R  E  L  A  N  D', 10.5 * dpmm,  11.36 * dpmm);

        // Reset
        context.font      = '300 '+(2.91*dpmm)+'px "HelveticaNeue-Light", "Helvetica Neue", Helvetica, Arial, sans-serif';
        context.fillStyle = main_color;
    }

    /*
     * Contact Details
     */
    var email      = document.getElementById('cb-email').value;
    var mobile     = document.getElementById('cb-mobile').value;
    var fax        = document.getElementById('cb-fax').value;
    var phone      = document.getElementById('cb-telephone').value;
    var department = document.getElementById('cb-department').value;
    var title      = document.getElementById('cb-title').value;
    var post_nominal_letters = document.getElementById('cb-post_nominal_letters').value;
    var name       = document.getElementById('cb-employee_name').value;

    indent         = 4 * dpmm;
    var tabbed     = 15.4545 * dpmm; // tabbed indent

    /* Print lines, starting from the bottom */
    line           = (canvas.height - 4 * dpmm) / line_height;

    // Website
    // text, x, y
    context.fillText('www.regeneron.com', indent, line * line_height);
    line--;

    // Email
    if (email != '')
    {
        context.fillText('Email:',  indent, line * line_height);
        context.fillText(email,     tabbed, line * line_height);
        line--;
    }
    // Mobile
    if (mobile != '')
    {
        context.fillText('Mobile:',  indent, line * line_height);
        context.fillText(mobile,     tabbed, line * line_height);
        line--;
    }
    // Fax
    if (fax != '')
    {
        context.fillText('Fax:',     indent, line * line_height);
        context.fillText(fax,       tabbed, line * line_height);
        line--;
    }
    // Phone
    if (phone != '')
    {
        context.fillText('Phone:',   indent, line * line_height);
        context.fillText(phone,      tabbed, line * line_height);
        line--;
    }
    // Department
    if (department != '')
    {
        context.fillText(department, indent, line * line_height);
        line--;
    }
    // Title
    if (title != '')
    {
        context.fillText(title,      indent, line * line_height);
        line--;
    }
    // Post-nominal letters
    if (post_nominal_letters != '')
    {
        font_size         = 6.5 * dppt;
        context.font      = '300 italic '+(font_size)+'px "HelveticaNeue-Light", "Helvetica Neue", Helvetica, Arial, sans-serif';
        context.fillText(post_nominal_letters, indent, line * line_height);
        line--;
    }
    // Employee name
    if (name != '')
    {
        font_size         = 9 * dppt;
        context.font      = '500 '+(font_size)+'px "Helvetica Neue", Helvetica, Arial, sans-serif';
        context.fillStyle = highlight;
        context.fillText(name,       indent, line * line_height);
    }
}
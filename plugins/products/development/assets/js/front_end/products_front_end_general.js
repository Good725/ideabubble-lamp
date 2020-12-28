$(document).ready(function()
{
    // Link entire "detailed" product item
    $('.product_item_list.detailed').on('click', function(e)
    {
        if ( ! $(e.target).is('a, :input'))
        {
            e.preventDefault();
            var href = $(this).find('.product_item_view_link a').attr('href');
            // Middle mouse/Ctrl/Cmd + click ? new tab : same tab;
            (e.ctrlKey || e.metaKey || e.which == 2) ? window.open(href, '_blank') : window.location.href = href;
        }
    });
});

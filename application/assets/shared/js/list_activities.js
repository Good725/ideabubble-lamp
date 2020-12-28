$(document).ready(function()
{
    if (document.getElementById('list_activities_server_side'))
    {
        var $table = $('.list_activities_table');
        $table.ready(function()
        {
            var ajax_settings = '/admin/settings/ajax_get_activities_datatable';
            var settings = {
                    "iDisplayLength" : 10,
                    "aLengthMenu"     : [10],
                    "sPaginationType" : "bootstrap",

                };
            var drawback_settings = {"fnDrawCallback" : function ()
                {
                    $('[rel="popover"]').popover({ html:true });
                    $('.list_activities_wrapper .dataTables_paginate').addClass('btn-group').find('a').addClass('btn');
                    generate_notes();
                }};
                $table.ib_serverSideTable(ajax_settings, settings, drawback_settings);

        });
    }
});

$(document).on('click', '.list_activities_table tbody tr', function()
{
    $(this).parents('tbody').find('.selected').removeClass('selected');
    this.className += ' selected';
    $(this).parents('.list_activities_wrapper')[0].getElementsByClassName('header_buttons')[0].style.visibility = 'visible';
});

$(document).on('click', '.add_activity_note', function(ev)
{
    ev.preventDefault();
    var $wrapper    = $(this).parents('.list_activities_wrapper');
    var selected_id = $wrapper.find('tbody .selected td:first-child').html();
    if (typeof selected_id == 'undefined')
    {
        // The "add note" option shouldn't appear until the user has selected a note. This is just in case.
        alert('No activity selected. Please click on an activity and try again.');
    }
    else
    {
        var $modal = $wrapper.find('.add_activity_note_modal');
        $modal[0].getElementsByClassName('activity_note_id')[0].value = selected_id;
        $modal.modal('show');
    }
});

$(document).on('click', '.save_activity_note_button', function()
{
    var $form  = $(this).parents('form');
    var $modal = $(this).parents('.modal');
    $.ajax(
    {
        url     : '/admin/notes/ajax_save_activity_note',
        type    : 'POST',
        data    : $form.serialize()
    }).done(function(data)
        {
            if (data == 'success')
            {
                generate_notes();
                $modal.modal('hide');
            }
        });
});

$(document).on('click', '.activities_tab', generate_notes);

function generate_notes()
{
    $('.activity-notes-icon').each(function()
    {
        var icon      = this;
        var id        = this.getAttribute('data-id');
        var item_type = this.getAttribute('data-item_type');
        var item_id   = this.getAttribute('data-item_id');
        $.ajax(
            {
                url  : '/admin/notes/ajax_get_notes_by_activity_id/'+id+'?item_type='+item_type,
                data : { item_type: item_type, item_id: item_id },
                type : 'POST'
            }).done(function(data)
            {
                if (typeof data == 'string' && data != '')
                {
                    icon.parentNode.setAttribute('data-content', data);
                    icon.parentNode.setAttribute('title', data);
                    icon.className += ' icon-book';
                }
            });
    });
}

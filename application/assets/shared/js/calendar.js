var calendar_dates = [];
$(document).ready(function()
{


    var hash = window.location.hash.substr(1);
    if (hash == undefined || hash == '') {
        $('#calendar_add_event').show();
    } else {
        $('#' + hash).show();

    }

    var activeTab = null;
    $(document).on('show.bs.tab', '[data-toggle="tab"]', function (e) {
        activeTab = e.target.hash.substr(1);
        switch (activeTab) {
            case 'calendar':
                $('#calendar_add_event').show();
                $('#calendar_add_type').hide();
                $('#calendar_add_rule').hide();
                break;
            case 'calendar_rule':
                $('#calendar_add_event').hide();
                $('#calendar_add_type').hide();
                $('#calendar_add_rule').show();
                break;
            case 'calendar_type':
                $('#calendar_add_event').hide();
                $('#calendar_add_type').show();
                $('#calendar_add_rule').hide();
                break;
            case 'calendar_event':
                $('#calendar_add_event').show();
                $('#calendar_add_type').hide();
                $('#calendar_add_rule').hide();
                break;
            default:
                $('#calendar_add_event').show();
                $('#calendar_add_type').hide();
                $('#calendar_add_rule').hide();
                break;
        }
        if (activeTab == null || activeTab == undefined) {
            $('#calendar_add_event').show();
        } else {

        }
    });
	$('#main-calendar').eventCalendar({
		eventsjson: '/admin/calendars/get_calendar_dates',
		jsonDateFormat: 'human',
		cacheJson: false
	});
});

$(document).on('click','#calendar_views table td [class^=icon]',function()
{
    var table = $(this).closest('table').attr('id');
    var icon = $(this).attr('class');
    var id = $(this).closest('tr').data('row_id');
    var item_type = $(this).closest('tr').data('item');
    var publish = $(this).closest("tr .publish");
    var deleted = $(this).closest("tr .delete");
    if ($(this).closest('td').hasClass('publish'))
    {
        $.post('ajax_publish',{id:id,table:table},function(result){
            if (result.status=='success') {
                if (icon == 'icon-ok') {
                    publish.html('<span class="icon-ban-circle"></span>');
                    publish.data('publish', 0);
                }
                else {
                    publish.html('<span class="icon-ok"></span>');
                    publish.data('publish', 1);
                }
            }
        },'json');
    }
    else
    {
        $.post('/admin/calendars/ajax_delete',{id:id,table:table},function(result){
            if (result.status=='success') {
                $('#'+ item_type + id).remove();
            }
        },'json');
    }
});


$(document).on('click','#calendar_add_event',function()
{
    $('#add_event_modal').modal();
});

$(document).on('click', '#btn_delete', function(){
    var id = $(this).attr('data-id');
    if (id === undefined) {
        return false;
    }
    var table = $(this).attr('data-item');
    $.post('/admin/calendars/ajax_delete',
        {
            id:id,
            table:table
        },function(result){
        console.log(result);
        if (result.status=='success') {
            window.location.href = '/admin/calendars/index';
        }
    },'json');
});
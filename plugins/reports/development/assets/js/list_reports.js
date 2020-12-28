$(document).ready(function(){
    reports = [];
    $('.dataTable').on('click','.remove_report',function(){
        var report_id = $(this).closest('tr').data('report_id');
        reports.push(report_id);
        $('#myModal').modal();
    });

    $("#delete_report").on('click',function(){
        delete_report(reports[0]);
        reports = [];
    });

    $("#cancel_delete").on('click',function(){
       reports = [];
    });

    $(".toggle_dashboard i").click(function(){
        var report_id = $(this).closest('tr').data('report_id');
            $.post('/admin/reports/dashboard',{report_id: report_id},function(){

            });
        if($(this).hasClass('icon-ok'))
        {
            $(this).removeClass('icon-ok');
            $(this).addClass('icon-remove');

        }
        else
        {
            $(this).removeClass('icon-remove');
            $(this).addClass('icon-ok');
        }
    });

    $('.toggle_favorite').change(function()
    {
        var report_id = $(this).closest('tr').data('report_id');
        $.post('/admin/reports/toggle_favorite',{report_id: report_id, is_favorite: this.checked });
    });

});

function delete_report(report_id)
{
    $.post('/admin/reports/delete_report',{report_id: report_id},function(){
        window.location.href = '/admin/reports/';
    });
}
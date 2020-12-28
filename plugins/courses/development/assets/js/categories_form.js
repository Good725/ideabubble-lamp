var myMinTime, myMaxTime, myInterval;
$(document).ready(function() {
    $.get('/admin/courses/get_schedule_min_start',function(data){
        myMinTime = data;
    });
    $.get('/admin/courses/get_schedule_max_start',function(data){
        myMaxTime = data;
    });
    $.get('/admin/courses/get_schedule_interval_setting',function(data){
        myInterval = $.parseJSON(data);
        myInterval != '' ? parseInt(myInterval) : 15;
    });

    initDateRangePicker();

    jQuery.extend(jQuery.validator.messages, {
        required: "Required!"
    });

    // CKEditor Configuration
    CKEDITOR.replace('description', {

            // Toolbar settings
            toolbar :
                [
                    ['Source'],
                    ['Format', 'Font', 'FontSize'],
                    ['Bold', 'Italic', 'Underline', '-', 'RemoveFormat'],
                    ['TextColor','BGColor'],
                    ['Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo'],
                    ['NumberedList', 'BulletedList', '-', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
                    ['Image', 'Table'],
                    ['Link','Unlink','Anchor','-','SpecialChar'],
                    [ 'Maximize', 'ShowBlocks']
                ],

            // Editor width
            width   : '100%'

        }
    );
    $("#form_add_edit_category").validate();
    $("#publish_yes").click(function(ev){
       ev.preventDefault();
       $("#publish").val('1');
    });
    $("#publish_no").click(function(ev){
        ev.preventDefault();
        $("#publish").val('0');
    });
    $("#btn_delete").click(function (ev) {
        ev.preventDefault();
        var id = $(this).data("id");
        $("#btn_delete_yes").data('id', id);
        $("#confirm_delete").modal();
    });
    $("#btn_delete_yes").click(function (ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        $.post('/admin/courses/remove_category', {id: id}, function (data) {
            if (data.redirect !== '' || data.redirect !== undefined) {
                window.location = data.redirect;
            } else {
                var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">X</a><strong>Error: </strong> ' + data.error_msg + '</div>';
                $("#main").prepend(smg);
            }
            $("#confirm_delete").modal('hide');

        }, "json");

    });

    $(document).on('click','.datetimepicker',function()
    {
        initDateRangePicker();
    });

    $('.save_button').click(function(){
        $("#redirect").val(this.getAttribute("data-redirect"));
        $("#form_add_edit_category").submit();
    });
});

function initDateRangePicker(){
    var from,
        to;

    from = $('#start_time');
    to = $('#end_time');

    Date.parseDate = function( input, format ){
        return moment(input,format).toDate();
    };
    Date.prototype.dateFormat = function( format ){
        return moment(this).format(format);
    };

    from.datetimepicker({
        datepicker : false,
        format: 'H:mm',
        formatTime: 'H:mm',
        minTime: myMinTime,
        maxTime: myMaxTime,
        step: myInterval
    });
    to.datetimepicker({
        datepicker: false,
        format: 'H:mm',
        formatTime: 'H:mm',
        minTime: myMinTime,
        maxTime: myMaxTime,
        step: myInterval
    });

}
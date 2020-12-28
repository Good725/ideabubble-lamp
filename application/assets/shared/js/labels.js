$(document).ready(function(){
    $("#add_label").on('click',function(){
        $.post('/admin/settings/add_label',{label:$("#label").val()},function(data){
            window.location.reload();
        });
    });
});

function add_label()
{
    $('#label_modal').modal();
}
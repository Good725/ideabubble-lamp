$(document).ready(function(){
    $(".save_btn").click(function(){
        $("#action").val($(this).data('action'));
        $("#project_edit_form").submit();
    });

    $("#project_related_projects").change(function(){
        if($(this).val() != "")
        {
            $("#add_related_button").show();
        }
        else
        {
            $("#add_related_button").hide();
        }
    });

    $("#add_related_button").click(function(){
        var project_id = $("#project_related_projects").val();
        reset_projects_list();
        //reload_projects_list();
        $.post('/admin/projects/ajax_get_project_as_related',{project_id: project_id,current: $("#id").val()},function(data){
            $("#projects_pane").append(data);
        });
    });

    $(".remove_project_link").click(function(){
        var project_id = $("#id").val();
        var related_project = $(this).data('id');
        $.post('/admin/projects/ajax_remove_related_project',{project_id: project_id,current: related_project},function(data){
           if(data == 1)
           {
               $(this).parent(".well").remove();
           }
        });
    });

    $("#project_select_image").change(function(){
        if($("#project_select_image").val() != 0)
        {
            $("#add_image_button").show();
        }
        else
        {
            $("#add_image_button").hide();
        }
    });

    $("#add_image_button").click(function()
    {
        var image_id   = $("#project_select_image").find(':selected').data('id');
        $.ajax({url: '/admin/projects/ajax_add_image_tr/'+image_id}).success(function(results)
        {
            $('#images_table').append(results);
        });
    });

    $('#images_table').on('click', '.icon-remove', function()
    {
        $(this).parents('tr').remove();
    });
});

function reset_projects_list()
{
    $("#project_related_projects").val('');
    $("#project_related_projects").change();
}
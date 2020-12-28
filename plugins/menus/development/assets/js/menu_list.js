jQuery(document).ready(function($){

    $("#menu_image").on('change',function(){
        var href = $('option:selected',this).data('link');
        if (href) {
            $("#image_preview").attr('src',href).show();
        }
        else {
            $("#image_preview").hide();
        }
    });

    $(document).on('click','.modal_image',function(){
        const row_id   = $(this).data('menu_id');
        const image_id = parseInt($(this).parents('tr').find('.menu_image').val()) || '';
        const html_attributes = $(this).parents('tr').find('.menu_html_attributes').val();

        $("#modal_row_id").val(row_id);
        $("#menu_image").val(image_id).change();
        $('#menu-edit-html_attributes').val(html_attributes);
    });

    $("#save_modal_image").on('click',function(){
        const $row = ($("#modal_row_id").val() == "")
            ? $('form[name="new_menu"]')
            : $('tr[data-id="' + $("#modal_row_id").val() + '"]');

        $row.find('.menu_image').val($("#menu_image").val());
        $row.find('.menu_html_attributes').val($("#menu-edit-html_attributes").val());
    });

    //Display the new menu
    $("#add_menu").click(function(event){
        event.preventDefault();
        $("#new_menu_div").toggle();

    });

    //Display "add different category"
    $("#add_menu_group").click(function(event){
        event.preventDefault();
        if($("#new_group input").attr('type') == 'hidden')
            $("#new_group").html('<input class="form-control" type="text" name="category" value="'+ group +'" />');
        else
            $("#new_group").html('<input type="hidden" name="category" value="'+ group +'" />');

    });

    // When the tab is changed, load the correct option dropdown in the 'new menu'
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        group = $('#menu_list_tabs').find('li.active [data-toggle]').text();
        set_group(group);
        $("#new_group input").val(group);
    });

    //same of above, just for the first time load.
    group = $('#menu_list_tabs').find('li.active [data-toggle]').text();
    if(group == ''){
        group = 'default';
    }
    set_group(group);
    $("#new_group input").val(group);

    //Change publish status, AJAX request
    $(".publish").on("click", function(event){
        var click_item = $(this);
        //Get the id from the id attribute
        var str = $(this).attr('id');
        var n=str.split("publish_");

        //Remove alerts, prevent stack

        $(".alert").remove();

        $.get('menus/publish/' + n[1], function(data) {
            if(data == 'success'){
                if($(click_item).html() == '<i class="icon-remove"></i>')
                    $(click_item).html('<i class="icon-ok"></i>');
                else
                    $(click_item).html('<i class="icon-remove"></i>');

                var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><strong>Success: </strong> Menu updated</div>';
                $("#main").prepend(smg);
            }
            else{
                var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>Warning: </strong> The menu can\'t be saved</div>';
                $("#main").prepend(smg);
            }
        }).error(function(){
                var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>Warning: </strong> Can\'t connect with the server</div>';
                $("#main").prepend(smg);
            });
    });

    //Save the clicked item for global access.
    var click_item;

    $(".remove").click(function(){
        click_item = $(this);
        $('#confirm_delete').modal();
    })
    //Delete selected menu, AJAX request
    $("#btn_delete_yes").on("click", function(event){

        //Get the id from the id attribute
        var str = $(click_item).attr('id');
        var n=str.split("remove_");
        $('#confirm_delete').modal('hide');

        //Remove alerts, prevent stack

        $(".alert").remove();

        $.get('menus/delete_menu/' + n[1], function(data) {
            if(data == 'success'){
                if($(click_item).parent().parent('tbody').children().size() == 2){
                    var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><strong>Success: </strong>The category will be deleted in the next update</div>';
                    $("#main").prepend(smg);
                }

                remove_childrens(n[1]);

                //Reload de dropdown menu
                set_group(group);


                var smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><strong>Success: </strong> Menu deleted</div>';
                $("#main").prepend(smg);
            }
            else{
                var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>Warning: </strong> The menu can\'t be deleted</div>';
                $("#main").prepend(smg);
            }
        }).error(function(){
                var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>Warning: </strong> Can\'t connect with the server</div>';
                $("#main").prepend(smg);
            });
    });
});
var group = '';
function set_group(group_name){
    group = group_name;
    jQuery("#new_menu_submenu").load('menus/get_option_dropdown',{ 'category': group_name});
}

function remove_childrens(id){
    $('tbody').children().each(function(i){
        if($(this).attr('data-parent') == id){
            //Recursive calls, remove nested items
            remove_childrens($(this).attr('data-id'));
        }
    });
    $('tbody [data-id="'+ id +'"]').remove();

}
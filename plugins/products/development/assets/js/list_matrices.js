$(document).ready(function(){
    $("#matrix_table").dataTable();

    $(document).on('click','.enabled i',function(){
        var matrix_id = $(this).closest('tr').data('matrix_id');
        var publish ;
        var obj = $(this);
        if($(this).hasClass('icon-ok'))
        {
            publish = 0;
        }
        else
        {
            publish = 1;
        }
        $.post('/admin/products/set_matrix',{matrix_id:matrix_id,action:'publish',value:publish})
            .done(function(){
            if(publish == 1)
            {
                obj.removeClass('icon-remove').addClass('icon-ok');
            }
            else
            {
                obj.removeClass('icon-ok').addClass('icon-remove');
            }
        });
    });

    $(document).on('click','b',function(){
        var matrix_id = $(this).closest('tr').data('matrix_id');
        $("#matrix_delete_modal").val(matrix_id);
    });

    $(document).on('click','#confirm_delete',function(){
        var matrix_id = $("#matrix_delete_modal").val();
        var publish = 1;
        $.post('/admin/products/set_matrix',{matrix_id:matrix_id,action:'delete',value:publish},function(){
            window.location.reload(true);
        });
    });
});
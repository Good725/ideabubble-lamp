$(document).ready(function(){

    $(".save_btn").click(function(){
        $("#action").val($(this).data('action'));
        $("#category_edit_form").submit();
    });

    $("#project_publish_toggle > button").click(function(){
        $("#publish").val($(this).val());
    });

    $(".delete_category").click(function(){
        $.post('/admin/reports/delete_category',{category_id:$(this).parent('tr').data('category_id')},function(result){
            if(result == "1")
            {
                window.location.href = '/admin/reports/categories';
            }
        });
    });
});
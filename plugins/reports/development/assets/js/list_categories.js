$(document).ready(function(){
    $(".publish").on('click','i.icon-ok',function(){
        var category_id = $(this).closest('tr').data('category_id');
        $.post('/admin/reports/toggle_publish_category',{category_id:category_id,publish:0});
        $(this).removeClass('icon-ok');
        $(this).addClass('icon-remove');
    });

    $(".publish").on('click','i.icon-remove',function(){
        var category_id = $(this).closest('tr').data('category_id');
        $.post('/admin/reports/toggle_publish_category',{category_id:category_id,publish:1});
        $(this).removeClass('icon-remove');
        $(this).addClass('icon-ok');
    });
});
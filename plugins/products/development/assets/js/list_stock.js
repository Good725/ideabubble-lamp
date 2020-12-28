$(document).ready(function(){
    var table = $("#stock_table").dataTable();

    $(document).on('change','.quantity_input',function(){
        var product_id = $(this).closest('tr').data('product_id');
        var option_id = $(this).closest('tr').data('option_id');
        var product_name = $(this).closest('tr').children('td.product_name').text();
        var quantity = $(this).val();
        $.post('/admin/products/update_stock',{product_id: product_id,option_id: option_id,quantity: $(this).val()},function(result){
            $(".alert_message").html('Product: <b>'+product_name+"</b> updated quantity - <b>"+quantity+"</b>");
            refresh_prices();
            $(".alert_message").show();
        });
    });

    $(document).on('change',".price_input",function(){
        var product_id = $(this).closest('tr').data('product_id');
        var option_id = $(this).closest('tr').data('option_id');
        var product_name = $(this).closest('tr').children('td.product_name').text();
        var price = $(this).val();
        $.post('/admin/products/update_stock',{product_id: product_id,option_id: option_id,price: $(this).val()},function(result){
            $(".alert_message").html('Product: <b>'+product_name+"</b> updated price - <b>"+price+"</b>");
            refresh_prices();
            $(".alert_message").show();
        });
    });

    $(document).on('click', '.toggle-publish-stock-option', function() {
        var i = $(this).children('i');

        if(i.hasClass('icon-ok'))
        {
            $(this).children('.option_publish').val(0);

            i.removeClass('icon-ok'    );
            i.addClass   ('icon-remove');
        } else {
            $(this).children('.option_publish').val(1);

            i.removeClass('icon-remove');
            i.addClass   ('icon-ok'    );
        }
        var product_id = $(this).closest('tr').data('product_id');
        var option_id = $(this).closest('tr').data('option_id');
        var product_name = $(this).closest('tr').children('td.product_name').text();
        $.post('/admin/products/update_stock',{product_id: product_id,option_id: option_id,publish: $(this).children('.option_publish').val()},function(result){
            $(".alert_message").html('Publish: <b>'+product_name+"</b> updated publish - <b>"+$(this).children('.option_publish').val()+"</b>");
            refresh_prices();
            $(".alert_message").show();
        });
    });

    refresh_prices();
});

function refresh_prices()
{
    $("#stock_table tr").each(function(){

        if($(this).find("input.price_input").val() != '' && $(this).children(".final_price").text() != "undefined")
        {
            $(this).children(".final_price").text((parseFloat($(this).children(".final_price").text()) + parseFloat($(this).find("input.price_input").val())).toFixed(2));
        }
    });
}
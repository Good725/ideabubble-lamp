/**
 * Created with JetBrains PhpStorm.
 * User: dale
 * Date: 10/02/2014
 * Time: 16:19
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function(){

    if($("#contact").val() != "" || ($("#contact").val() != "add"))
    {
        var customer = $("#contact").val();
        get_contact(customer,'contact_');
    }

    $("#contact").on("change",function(){
       var customer = $(this).val();
        get_contact(customer,'contact_');
    });

    if($("#billing_contact").val() != "" || $("#billing_contact").val() != "add")
    {
        var customer = $("#billing_contact").val();
        get_contact(customer,"billing_");
    }

    $("#billing_contact").on("change",function(){
        var customer = $(this).val();
        get_contact(customer,"billing_");
    });

    $("#contact_email").on('change',function(){
        if($("#id").val() == "new" && $("#contact_email").val() != '')
        {
            $.post('/admin/extra/check_email',{email: $("#contact_email").val()},function(data){
                console.log(data);
                if(data == '1')
                {
                    $("#email_error").show();
                    $("#customer_edit input[type='submit']").hide();
                }
                else
                {
                    $("#email_error").hide();
                    $("#customer_edit input[type='submit']").show();
                }
            });
        }
    });
});

function get_contact(customer,prefix)
{
    if(prefix == null || prefix == "undefined")
    {
        prefix = "";
    }
    if (!customer)
        return;
    $.post('/admin/contacts2/get_contact',{'id': customer},function(data){
        $("#"+prefix+"first_name").val(data.first_name);
        $("#"+prefix+"last_name").val(data.last_name);
        $("#"+prefix+"email").val(data.email);
        $("#"+prefix+"phone").val(data.phone);
    },'json');
}
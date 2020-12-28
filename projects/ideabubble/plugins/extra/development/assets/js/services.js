$(document).ready(function() {
    $('#btn_save').on('click', function()
    {
        var id = $('#service_id').val();
        if (id != '')
            $('#service_redirect').val('admin/extra/edit_service/'+id);
        $('#form_add_service').submit();
    });

    $('#btn_save_exit').on('click', function()
    {
        $('#service_redirect').val('admin/extra/services');
        $('#form_add_service').submit();
    });
});

$('#btn_invoice').on('click', function()
{
    $('#invoice_create').modal();
});

$('#service_btn_delete').on('click', function()
{
    $('#service_confirm_delete').modal();
});

function view_details()
{
    var id = $('#service_company_id').val();
    $.ajax({
        url      : '/admin/extra/ajax_get_customer_details/'+id,
        dataType : 'json',
        async    : false
    }).done(function(result){
         if(typeof(result['contact']) == 'string' && typeof(result['billing_contact']) == 'string'){
           $('#contact_details').show();
           $('#main_contact').find('.contact_name').html('No data');
           $('#main_contact').find('.contact_phone').html('');
           $('#main_contact').find('.contact_email').html('');
           $('#billing_contact').find('.contact_name').html('No data');
           $('#billing_contact').find('.contact_phone').html('');
           $('#billing_contact').find('.contact_email').html('');
            } else {
            var contact = result['contact'];
                if (contact['phone'] == '') {
                    contact['phone'] = contact['mobile'];
                }
            var billing_contact = result['billing_contact'];
                if (billing_contact['phone'] == '') {
                    billing_contact['phone'] = billing_contact['mobile'];
                }
            $('#contact_details').show();
            $('#main_contact').find('.contact_name').html(contact['first_name']);
            $('#main_contact').find('.contact_phone').html(contact['phone']);
            $('#main_contact').find('.contact_email').html(contact['email']);
            $('#billing_contact').find('.contact_name').html(billing_contact['first_name']);
            $('#billing_contact').find('.contact_phone').html(billing_contact['phone']);
            $('#billing_contact').find('.contact_email').html(billing_contact['email']);
         }

    });
}

// Replace with something that gets expiration from whois data
function getExpiryDate()
{
    var start = new Date();

    var d = start.getDate();
    var m = start.getMonth()+1;
    var y = start.getFullYear()+1;

    var expiry = new Date(y, m, d);
    var expiry_date = ('0'+expiry.getDate()).slice(-2)+'-'+('0'+expiry.getMonth()).slice(-2)+'-'+expiry.getFullYear();

    $('#service_date_end').val(expiry_date);
}
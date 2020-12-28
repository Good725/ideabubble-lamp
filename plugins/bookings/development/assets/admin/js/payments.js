document.addEventListener("DOMContentLoaded", function(event) {
   $("#payment_type_selector").on('change',function(){
        var payment_type = document.getElementById('payment_type_selector').value;

       if(payment_type == 1)
       {
            $("#cash_payment_modal").modal();
       }
       else if(payment_type == 2 || payment_type == 3)
       {
            $("#card_payment_form").show();
       }
       else if(payment_type == 4)
       {
            $("#cheque_payment_form").show();
       }
   });

    $(document).on('click','#add_cash_payment',function(){
        add_payment_row('Cash',document.getElementById('cash_payment_amount').value);
    });
});

function add_payment_row(type,amount)
{
    var html = '<tr>' + '<td>'+type+'</td>'+'<td>'+amount+'</td>'+'<td><i class="icon-remove"></i></td>'+'</tr>'
    $("#payments_table tbody").append(html);
}
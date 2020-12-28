var transaction_id = '';
//Transactions
$(document).on('click', '[href="#family-details-tab"]', function(){
    $('[href="#family-member-details-tab"]').click();
});

function ajax_load_member_accounts_actions(callback)
{
    // change actions menu to load default menu options
    transaction_id = '';
    $('#header_buttons').empty().load('/admin/contacts3/ajax_load_accounts_actions');

    $('.modal_boxes').html('');
    loadTransactions('member', callback);
}

$(document).on('click', '[href="#family-member-accounts-tab"]', function(){
    if ($(this).attr("loaddata") != "no") {
        ajax_load_member_accounts_actions();
    }
});

function ajax_load_accounts_actions(callback)
{
    // change actions menu to load default menu options
    transaction_id = '';
    $('#family_header_buttons').empty().load('/admin/contacts3/ajax_load_accounts_actions');

    $('.modal_boxes').html('');
    loadTransactions('family', callback);
}

$(document).on('click', '[href="#family-accounts-tab"]', function(){
    if ($(this).attr("loaddata") != "no") {
        ajax_load_accounts_actions();
    }
});

function ajax_load_activities_actions(callback)
{
    transaction_id = '';
    $('#header_buttons').empty().load('/admin/contacts3/ajax_load_accounts_actions');

    $('.modal_boxes').html('');
    loadTransactions('activities', callback);
}

$(document).on('click', '[href="#family-activities-tab"]', function()
{
    if ($(this).attr("loaddata") != "no") {
        ajax_load_activities_actions();
    }
});

function loadTransactions(table, callback)
{
    var params,
        tab,
        tableId;

    if(table == 'family')
    {
        params = 'family_id=' + $('#family_id').val();
        tab = $('#family-accounts-tab');
        tableId = 'family_transaction_table';

        tab.find('.content-area').load('/admin/contacts3/ajax_get_family_accounts?' + params, function() {
            $(this).find('.dataTable').attr('id', tableId).dataTable({"aaSorting": []});
            if (callback) {
                callback();
            }
        });
    }
    else if(table == 'activities')
    {
        params = 'family_id=' + $('#family_id').val();
        tab = $('#family-activities-tab');
        tableId = 'family_activities_table';

        tab.find('.content-area').load('/admin/contacts3/ajax_get_family_activities?' + params, function() {
            $(this).find('.dataTable').attr('id', tableId).dataTable({"aaSorting": []});
            if (callback) {
                callback();
            }
        });
    }
    else
    {
        params = 'contact_id=' + $('#contact_id').val();
        tab = $('#family-member-accounts-tab');
        tableId = 'family_member_transaction_table';
        tab.find('.content-area').load('/admin/contacts3/ajax_get_family_member_accounts?' + params, function() {
            $(this).find('.dataTable').attr('id', tableId).dataTable({"aaSorting": []});
            if (callback) {
                callback();
            }
            $(this).find('.popinit').popover({trigger:'hover', container: 'body'});
        });
    }
}

$(document).on("change", "#modal_make_transaction_booking", function(){
    $.post(
        "/admin/bookings/booking_linked_schedules",
        {
            booking_id: this.value
        },
        function (response) {
            $("#modal_make_transaction_booking_schedule").html('<option value=""></option>');
            for(var i in response) {
                $("#modal_make_transaction_booking_schedule").append('<option value="' + response[i].id + '">' + response[i].name + '</option>');
            }
        }
    )
});

$(document).on('click', '#make_transaction_modal_btn', function()
{
    // Added a variable for the transaction type
    var booking_id = $("#modal_make_transaction_booking option:selected").val(),
        transaction_type = parseInt($("#modal_make_transaction_type option:selected").val()),
        total = $('#modal_make_transaction_total').val(),
        alerts = $('#make_transaction_modal_form .alert-area');
    // Save a transaction without a booking ID

    if ((total != 0 ) && transaction_type)
    {
        // Journal Transaction type -> Journal Credit
        if (transaction_type == 3)
        {
            booking_id = null ;
            saveTransaction();
        }
        else if (booking_id)
        {
            saveTransaction();
        }
        else
        {
            alerts.add_alert('Please fill in the Booking ID field.');
        }
    }
    else
    {
        alerts.add_alert('Please fill in the required form fields.');
    }
    return false;
});

$(document).on('click', '.make_transaction', function()
{
    var contact;
    if ($(this).parent('#family-member-accounts-tab').length > 0)
    {
        contact = $('#contact_id').val();
    }
    else
    {
        contact = $('#primary_contact_id').val();
    }
    if (contact == '')
    {
        $('#no_primary_alert_modal').modal();
    }
    else
    {
        var modal_box  = $('#make_transaction_modal');
        $("#make_transaction_modal_form")[0].reset();
        $('#modal_make_transaction_id').val('');
        $("#modal_make_transaction_type").val('');
        $("#modal_make_transaction_amount").val('0.00');
        $("#modal_make_transaction_admin_fee").val('0.00');
        $("#modal_make_transaction_total").val('0.00');
        $("#modal_make_transaction_booking").html('<option value="">Select Booking</option>');
        buildTransactionModalBox(modal_box);
    }
    return false;
});

$(document).on('click', '.edit_transaction', function(ev){
    ev.stopPropagation();
    var contact;
    if ($(this).parent('#family-member-accounts-tab').length > 0)
    {
        contact = $('#contact_id').val();
    }
    else
    {
        contact = $('#primary_contact_id').val();
    }
    if (contact == '')
    {
        $('#no_primary_alert_modal').modal();
    }
    else
    {
        var alerts = $('#make_transaction_modal_form .alert-area'),
            transaction_id = $(this).parent().data('transaction_id'),
            modal_box = $('#make_transaction_modal'),
            contact_name = $('#modal_make_transaction_contact_name'),
            total_input = $('#modal_make_transaction_total');

        $.ajax({
            url: '/admin/bookingstransactions/ajax_get_transaction?id=' + transaction_id,
            dataType: 'json'
        })
            .success(function(result){
                if(modal_box.parents('#family-accounts-tab').length){
                    contact_name.val($('#edit_family_heading h2').text());
                } else {
                    contact_name.val($('#edit_family_member_heading h2').text());
                }
                modal_box.find('h3').text('Save Transaction ID: ' + transaction_id);
                $('#modal_make_transaction_id').val(transaction_id);
                $('#modal_make_transaction_type').val(result.type);
                $('#modal_make_payment_transaction_id').val(result.transaction_id);
                $('#modal_make_transaction_amount').val(result.amount);
                $('#modal_make_transaction_discount').val(result.discount);
                $('#modal_make_transaction_admin_fee').val(result.fee);
                total_input.val(result.total);
                $('#modal_make_transaction_booking').val(result.booking_id);
                $('#make_transaction_modal_form h3').text('Edit Transaction ID: ' + transaction_id);
                //calculateTotal(result.amount, result.fee, total_input);
                buildTransactionModalBox(modal_box, result.booking_id);
            })
            .error(function()
            {
                alerts.add_alert('The server did not respond. Please try again later.', 'danger popup_box');
                remove_popbox();
            });
    }
});

function buildTransactionModalBox(modal_box, booking_id){
    var alerts = $('#make_transaction_modal_form .alert-area'),
        params  = 'contact_id=' + $('#contact_id').val(),
        contact_id = $('#contact_id').val(),
        family_id = $('#family_id').val(),
        contact_name = $('#modal_make_transaction_contact_name');

    contact_name.val($('#edit_family_member_heading h2').text());
    if(modal_box.parents('#family-accounts-tab').length){
        params  = 'family_id=' + $('#family_id').val();
        contact_name.val($('#edit_family_heading h2').text());
    }
    $.ajax({
        url: '/admin/bookingstransactions/ajax_get_bookings?' + params,
        dataType: 'json'})
        .success(function(results){
            $('#modal_make_transaction_contact_id').val(contact_id);
            $('#modal_make_transaction_family_id').val(family_id);
            if(typeof results !== 'undefined' && results.length > 0){
                var options = '',
                    selected = '';
                for(var key in results){
                    if(typeof booking_id != 'undefined' && booking_id == results[key].booking_id){
                        selected = 'selected';
                    } else {
                        selected = '';
                    }
                    options += '<option value ="' + results[key].booking_id + '" ' + selected +'>' + 
									results[key].student + '; ' +
                                    results[key].category + ' - ' +
									results[key].schedule_title + '; ' +
									results[key].booking_id + 
								'</option>';
                }
                $('#modal_make_transaction_booking').append(options);
            }
            else
            {
                alerts.add_alert('The contact doesn\'t have any bookings.');
            }
            $(modal_box).modal();
        })
        .error(function(){
            alerts.add_alert('The server did not respond. Please try again later.', 'danger popup_box');
            remove_popbox();
        });
}

$(document).on('click', '.delete_transaction', function(ev){
    ev.stopPropagation();
    var transaction_id = $(this).parent().data('transaction_id'),
        alerts = $('#delete_modal .alert-area');
    $('#delete_modal h3').text('Delete Transaction ID: ' + transaction_id);
    $('#modal_delete_id').val(transaction_id);
    $('#modal_controller').val('transactions');
    $('#delete_modal').modal();
});

function saveTransaction ()
{
    var alerts = $('#make_transaction_modal_form .alert-area'),
        form = $('#make_transaction_modal_form');
    $.ajax({
        type: 'POST',
        url: '/admin/bookingstransactions/ajax_save_transaction',
        data: form.serialize(),
        dataType: 'json'
    })
    .done(function(results)
    {
        if(results.status == 'success')
        {
            alerts.add_alert(results.message, 'success');
            setTimeout(function(){
                $('#make_transaction_modal').modal('hide');
                setTimeout(function(){
                    var table = 'member';
                    if(alerts.parents('#family-accounts-tab').length){
                        table = 'family';
                    }
                    loadTransactions(table);
                }, 1300);
            }, 1500);
        }
        else
        {
            alerts.add_alert(results.message);
        }
    })
    .error(function()
    {
        alerts.add_alert('The server did not respond. Please try again later.', 'danger popup_box');
        remove_popbox();
    });
}

$(document).on('keyup','#modal_make_transaction_amount,#modal_make_transaction_admin_fee', function(){
    var amount = parseFloat($('#modal_make_transaction_amount').val()),
        fee =  parseFloat($('#modal_make_transaction_admin_fee').val()),
        total = $('#modal_make_transaction_total');
    calculateTotal(amount, fee, total);
});

//Payments
$(document).on('click', '.transaction-row', function(){
    transaction_id = $(this).data('transaction_id');
    var transaction_balance = $(this).data('transaction_balance');
    $(this).parent().find('tr').removeClass('selected');
    $(this).addClass('selected');
    load_payment(transaction_id,transaction_balance);
});

$(document).on('click', '#make_payment_modal_btn', function(ev)
{
    ev.preventDefault();
    if (check_btransactions()) {
        make_payment();
    }
});

$(document).on('change', '#modal_make_payment_create_journal', function(){
    if ($('#modal_make_payment_create_journal').prop('checked', true))
    {
        $('#modal_make_payment_create_journal').val('create');
    }
    else
    {
        $('#modal_make_payment_create_journal').val('');
    }
});

$(document).on('change','#modal_make_payment_journal_payment', function(){
    document.getElementById('modal_make_payment_total_due').value = $(this).find(':selected').data('amount');
});
//$(document).on('click', '$("[name=journal_type]")', function(){
//    $("[name=journal_type]").val(this.checked);
//});

$(document).on('change','#modal_make_payment_status',function() {
    $("#modal_make_payment_journal_payment").prop("selectedIndex", -1);
    $("#modal_make_payment_journal_payment option").each(function (){
        if (parseInt($(this).data("settlement-id"))) {
            this.disabled = false;
        }
    });
    if ($(this).val() === '4') { // cancel payment; disable settled payments
        $("#modal_make_payment_journal_payment option").each(function (){
            if (parseInt($(this).data("settlement-id"))) {
                this.disabled = true;
            }
        });
    }

    if ($(this).val() === '5')
    {
        $('#make_payment_modal_type > option').each(function() {
            if ($(this).val() == 'transfer')
            {
                $(this).show();
                $(this).prop('selected', true);
            }
            else
            {
                $(this).hide();
            }
        });
    }
    else
    {
        $('#make_payment_modal_type > option').each(function() {
            if ($(this).val() == 'transfer')
            {
                $(this).hide();
            }
            else if (this.value == '')
            {
                $(this).show();
                $(this).prop('selected', true);
            }
            else
            {
                $(this).show();
            }
        });
    }

    $("#credit_journal_option .journal_title").addClass("hidden");
    if ($("#modal_make_payment_status option:selected").data("credit") == "-1") {
        $("#modal_make_payment_total_due").prop("readonly", false);
        $("#credit_journal_option .journal_title.refund").removeClass("hidden");
        $('#credit_journal_option').show();
    } else {
        $("#credit_journal_option .journal_title.exceed").removeClass("hidden");
        $("#modal_make_payment_total_due").prop("readonly", true);
    }
});

function check_btransactions()
{
    if ($(".btransaction_payment_amount").length > 0) {
        var btransactions_total = 0;
        $(".btransaction_payment_amount").each(function () {
            var amount = parseFloat(this.value);
            if (!isNaN(amount)) {
                btransactions_total += amount;
            }
        });

        var payment_amount = parseFloat($("#modal_make_payment_amount").val());
        if (btransactions_total != payment_amount) {
            $("#modal_payment_amount_does_not_match").modal();
            return false;
        }
    }
    return true;
}

function make_payment()
{
    var credit = $('#make_payment_modal_payment_type').val();
    var data = {};
    if (credit == 1)
    {
        var btransactions_payments = [];
        var btransactions_payments_total = 0;
        var modal_make_payment_amount_total = parseFloat($("#modal_make_payment_amount").val());
        var make_payment_outstanding = $('#modal_make_payment_outstanding').val();
        $(".btransaction_payment_amount").each(function(){
            btransactions_payments.push(
                {
                    transaction_id: $(this).data("transaction_id"),
                    amount: $(this).val()
                }
            );
            if (!isNaN(parseFloat($(this).val()))) {
                btransactions_payments_total += parseFloat($(this).val());
            }
        });

        if (btransactions_payments_total > 0 && btransactions_payments_total < modal_make_payment_amount_total) {
            $("#make_payment_modal .alert-area").html("<div class='alert'><a class='close' data-dismiss='alert'>×</a>Totals do not match</div>");
            return false;
        }

        data = {
            credit:credit,
            id:$('#modal_make_payment_id').val(),
            transaction_id:document.getElementById('modal_make_payment_transaction_id').value,
            booking_id:document.getElementById('modal_make_payment_booking_id').value,
            btransactions_payments: btransactions_payments,
            transaction_balance:document.getElementById('modal_make_payment_transaction_balance').value,
            booking_balance:document.getElementById('modal_make_payment_booking_balance').value,
            amount:document.getElementById('modal_make_payment_amount').value,
            type:document.getElementById('make_payment_modal_type').value,
            bank_fee:document.getElementById('modal_make_payment_bank_fee').value,
            status:document.getElementById('modal_make_payment_status').value,
            note:document.getElementById('modal_make_payment_note').value,
            name_cheque:document.getElementById('modal_make_payment_cheque_record_name').value,
            ccName:document.getElementById('modal_make_payment_card_name').value,
            ccType:document.getElementById('modal_make_payment_card_type').value,
            ccNum:document.getElementById('modal_make_payment_card_number').value,
            ccv:document.getElementById('modal_make_payment_card_cvv').value,
            ccExpMM:document.getElementById('modal_make_payment_card_expired_m').value,
            ccExpYY:document.getElementById('modal_make_payment_card_expired_y').value,
            create_journal:document.getElementById('modal_make_payment_create_journal').value,
            journal_type:document.querySelector('input[name="journal_type"]:checked').value,
            credit_transaction:'',
            contact_id:$('#contact_id').val(),
            family_id:$('#family_id').val(),
            outstanding:make_payment_outstanding - modal_make_payment_amount_total,
            send_backend_booking_emails:       $('#make_payment_modal_btn').data('send_backend_booking_emails') || '0',
        };
        if (data.status == 5)
        {
            data.credit_transaction = $("input[name='use_credit']:checked").val();
        }
    }
    else
    {
        data = {
            credit:credit,
            id:$('#modal_make_payment_id').val(),
            transaction_id:document.getElementById('modal_make_payment_transaction_id').value,
            journal_payment_id:$('#modal_make_payment_journal_payment').find(':selected').val(),
            status:document.getElementById('modal_make_payment_status').value,
            note:document.getElementById('modal_make_payment_note').value+' ' + $('#modal_make_payment_journal_payment').find(':selected').html(),
            total:$('#modal_make_payment_total_due').val(),
            contact_id:$('#contact_id').val(),
            family_id:$('#family_id').val()
        };

        if ($("#modal_make_payment_status option:selected").data("credit") == "-1") {
            data.create_journal = document.getElementById('modal_make_payment_create_journal').value;
            data.journal_type = document.querySelector('input[name="journal_type"]:checked').value;
        }
    }

    if ($('#contact_id') != undefined)
    {
        data.contact_id = $('#contact_id').val();
    }
    if ($('#family_id') != undefined)
    {
        data.family_id = $('#family_id').val();
    }

    var type  = $('#make_payment_modal_type').val(),
        alerts = $('#make_payment_modal .alert-area'),
        balance = parseFloat($('#modal_make_payment_transaction_balance').val()),
        status = $('#modal_make_payment_status').val(),
        transfer_balance = 0,
        pay_balance = parseFloat($('#modal_make_payment_transaction_balance').val()) - parseFloat($('#modal_make_payment_amount').val());
        if (parseFloat($('#modal_make_payment_booking_balance').val()) > 0) {
            pay_balance = parseFloat($('#modal_make_payment_booking_balance').val()) - parseFloat($('#modal_make_payment_amount').val());
        }
        if (btransactions_payments_total > 0) {
            pay_balance = btransactions_payments_total - parseFloat($('#modal_make_payment_amount').val());
        }
        valid = true;
    if (status == 5)
    {
        transfer_balance = parseFloat($('#modal_make_payment_available_balance').val()) - parseFloat($('#modal_make_payment_amount').val());
    }
    if (credit == 1)
    {
        if (pay_balance < 0)
        {
            if (status == 5)
            {
                alerts.add_alert('<div class="alert">The amount is more than the balance of the transaction: &euro;'+$('#modal_make_payment_transaction_balance').val()
                                 +'<br>Please amend the amount');
            }
            else
            {
                $('#modal_make_payment_overpay_alert').html('The amount is more than the balance of the transaction: &euro;'+$('#modal_make_payment_transaction_balance').val());
                alerts.add_alert('<div class="alert">The amount is more than the balance of the transaction: &euro;'+$('#modal_make_payment_transaction_balance').val()
                                 +'<br>Please amend the amount or select the options for adding credit to the account');
                $('#credit_journal_option').show();
            }
        }
        if (transfer_balance < 0)
        {
            alerts.add_alert('<div class="alert">The amount is more than the available credit on your account: &euro;'+$('#modal_make_payment_available_balance').val()
                             +'<br>Please amend the amount.');
        }
        if((pay_balance < 0 && $('#modal_make_payment_create_journal').is(':checked') && status == 2)
            || (pay_balance >=0)
            || (balance < 0 && ! (status == 1 || status == 4))
            || (pay_balance < 0 && (status == 3 || status == 4 || status == 6))
            || (transfer_balance >= 0 && status == 5 && pay_balance >=0)
        )
        {
            if(type == 'card' && !$('#modal_make_payment_id').val())
            {
                var card_type = $('#modal_make_payment_card_type option:selected').val(),
                    expired_m = $('#modal_make_payment_card_expired_m option:selected').val(),
                    expired_y = $('#modal_make_payment_card_expired_y option:selected').val();
                if(!card_type || !expired_m || !expired_y){
                    valid = false;
                }
            }
            if (valid)
            {
                savePayment(data);
            }
            else
            {
                alerts.add_alert('Please fill in the required form fields.', 'warning');
            }
        }
        else
        {
            alerts.add_alert('Please fill in the required form fields.');
        }
    }
    else
    {
        savePayment(data);
    }

}

$(document).on('change', '#make_payment_modal_type', function(){
    var type = $(this).val(),
        header = $('#main_payment_info h4'),
        modal = $('#make_payment_modal'),
		modal_dialog = modal.find('.modal-dialog'),
        card_info = $('#card_payment_info'),
        cheque_info = $('#cheque_options_info'),
		columns = $('.make_payment_modal_column');

    switch(type){
        case 'cash':
            modal_dialog.animate({width:'600px'}, 1000);
			columns.removeClass('col-sm-6');
            card_info.css('display','none');
            cheque_info.css('display','none');
            header.text('Payment via Cash Details');
            break;
        case 'cheque':
            header.text('Payment via Cheque Details');
            modal_dialog.animate({width:'1000px'}, 1000);
			columns.addClass('col-sm-6');
            card_info.css('display','none');
            cheque_info.css('display','block');
            header.text('Payment via Cheque Details');
            break;
        case 'card':
            header.text('Payment via Card Details');
            modal_dialog.animate({width:'1000px'}, 1000);
			columns.addClass('col-sm-6');
            $('#make_payment_modal_form').css('display', 'inline-flex');
            card_info.css('display','block');
            cheque_info.css('display','none');
            var year  = parseInt(new Date().getFullYear()),
                list = '';
            for(var i = 0; i < 20; i++) {
                var value = year + i;
                var text = year + i;
                value = value.toString().substr(2);
                list += '<option value="' + value + '">' + text + '</option>'
            }
            $('#modal_make_payment_card_expired_y').append(list);
            break;
    }
});

/**
 * Save the Payment form
 * @param form
 */
function savePayment(data){
    $('#make_payment_modal_btn').prop("disabled", true);
    var alerts = $('#make_payment_modal .alert-area');
    var balance_data = {
        contact_id:data.contact_id,
        family_id:data.family_id
    };
    $.ajax({
        type: 'POST',
        url: '/admin/payments/ajax_save_payment',
        data: data,
        dataType: 'json'
    })
    .done(function(results){
        if(results.status == 'success'){
            $('#family_balance_status span').html('');
            $('#family_balance_status span').html('Balance = -' + data.outstanding);
            $('#member_balance_status span').html('');
            $('#member_balance_status span').html('Balance = -' + data.outstanding);
            alerts.add_alert(results.message, 'success');
            if (data.booking_id) {
                $("[href='#family-member-bookings-tab']").click();
                $("#make_payment_modal").modal('hide');
            } else {
                display_balance(
                    balance_data,
                    function () {
                        var table = 'member';
                        if (alerts.parents('#family-accounts-tab').length) {
                            table = 'family';
                        }
                        $("#make_payment_modal_form")[0].reset();
                        $('#make_payment_modal .close').click();
                        if ($('#family-accounts-tab').is(':visible')) {
                            $('[href="#family-accounts-tab"]').click();
                        }
                        else if ($('#family-member-accounts-tab').is(':visible')) {
                            $('[href="#family-member-accounts-tab"]').click();
                        }
                        loadTransactions(table);
                    }
                );
            }
        }
        else
        {
            $('#make_payment_modal_btn').prop("disabled", false);
            alerts.add_alert(results.message);
        }
    })
    .error(function(){
        alerts.add_alert('The server did not respond. Please try again later.', 'danger popup_box');
        remove_popbox();
    });

}

$(document).on('click', '.make_payment', function(ev)
{
    ev.stopPropagation();
    ev.preventDefault();
    var contact;
    if ($(this).parents('#edit_family_member_wrapper').length > 0)
    {
        contact = $('#contact_id').val();
    }
    else
    {
        contact = $('#primary_contact_id').val();
    }
    if (contact == '')
    {
        $('#no_primary_alert_modal').modal();
    }
    else
    {
        var data = {
            transaction_id : transaction_id,
            contact_id: contact,
            credit:1
        };
        make_payment_modal(data);
    }
});

$(document).on('click', '.edit_payment_plan', function(ev) {
    ev.stopPropagation();
    ev.preventDefault();
    var contact_id;
    if ($(this).parents('#edit_family_member_wrapper').length > 0) {
        contact_id = $('#contact_id').val();
    } else {
        contact_id = $('#primary_contact_id').val();
    }
    if (contact_id == '') {
        $('#no_primary_alert_modal').modal();
    } else {
        if (transaction_id == '') {
            $('#alert_modal').modal();
        }

        var data = {
            transaction_id : transaction_id,
            contact_id: contact_id,
        };

        make_payment_plan_modal(data);
    }
});

function make_payment_plan_modal(data)
{
    if (transaction_id == '' && data.transaction_id == '') {
        $('#alert_modal').modal();
    } else {
        $.ajax({
            url:'/admin/payments/payment_plan_modal',
            data:data,
            datatype:'json',
            type:'POST'
        }).success(function(results){
            $('#make_payment_plan_modal').remove();
            $('body').append(results);
            $('#make_payment_plan_modal').modal();
            $("#payment_plan_starts").datetimepicker({
                format: 'd-m-Y',
                timepicker: false
            });
        });
    }
}

$(document).on('change', '#payment_plan_term_type', function(e) {
    e.stopPropagation();
    e.preventDefault();

    var form = $(this).parents('form');


    if (this.value == "custom") {
        $("#payment_plan_interest_type, #payment_plan_interest").prop("disabled", true);
    } else {
        $("#payment_plan_interest_type, #payment_plan_interest").prop("disabled", false);
    }
});

$(document).on('click', '#payment_plan_preview_btn', function(e) {
    e.stopPropagation();
    e.preventDefault();

    var form = $(this).parents('form');


    calculate_payment_plan(form, function(response){
        var $tbody = $("#payment_plan_installments tbody");
        $tbody.html("");
        var readonly = $("#payment_plan_term_type").val() != "custom" ? 'readonly="readonly" ' : '';
        for (var i in response) {
            response[i].due = new Date(response[i].due).dateFormat('d/m/Y');
            $tbody.append(
                '<tr class="installment">' +
                '<td>' + parseInt(parseInt(i) + 1) + '</td>' +
                '<td><input class="amount" type="text" name="installment[' + i + '][amount]" value="' + parseFloat(response[i].amount + response[i].adjustment) + '" ' + readonly + ' style="width: 50px;" /></td>' +
                '<td><input class="interest" type="text" name="installment[' + i + '][interest]" value="' + response[i].interest + (response[i].penalty > 0 ? ' (+' + response[i].penalty + ')' : '') + '" ' + readonly + '  style="width: 50px;"/></td>' +
                '<td><input class="total" type="text" name="installment[' + i + '][total]" value="' + response[i].total + '" ' + readonly + ' style="width: 50px;"/></td>' +
                '<td><input class="due date" type="text" name="installment[' + i + '][due]" value="' + response[i].due + '" ' + readonly + ' style="width: 75px;"/></td>' +
                '<td>' + response[i].status + '</td>' +
                '<td class="balance">' + response[i].balance + '</td>' +
                '</tr>'
            );
        }

        // allow changing timeslots only for custom plans
        if ($("#payment_plan_term_type").val() == "custom") {
            $tbody.find(".due.date").datetimepicker({
                format: ibcms.date_format.replace("dd", "d").replace("mm", "m").replace("yyyy", "Y"),
                timepicker: false
            });
        }
    });
});

$(document).on("change", "#payment_plan_installments .amount, #payment_plan_installments .interest", function(){

    var balance = parseFloat($("#payment_plan_outstanding").val());
    $("#payment_plan_installments tbody > tr").each(function(){
        var amount = parseFloat($(this).find(".amount").val());
        var interest = parseFloat($(this).find(".interest").val());
        var total = 0;
        if (!isNaN(amount) && !isNaN(interest)) {
            total = amount + interest;
            balance -= amount;
            $(this).find(".total").val(total);
            $(this).find(".balance").html(balance.toFixed());
        }
    });
});

function calculate_payment_plan(form, callback)
{
    $.post(
        '/admin/payments/calculate_payment_plan',
        {
            amount: $(form).find('#payment_plan_outstanding').val(),
            deposit: $(form).find('#payment_plan_deposit').val(),
            adjustment: $(form).find('#payment_plan_adjustment').val(),
            terms: $(form).find('#payment_plan_terms').val(),
            term_type: $(form).find('#payment_plan_term_type').val(),
            interest_type: $(form).find('#payment_plan_interest_type').val(),
            interest: $(form).find('#payment_plan_interest').val(),
            starts: $(form).find('#payment_plan_starts').val()
        },
        function (response) {
            if (callback) {
                callback(response);
            }
        }
    );
}

$(document).on("change", "#payment_plan_interest, #payment_plan_interest_type, #payment_plan_term_type, #payment_plan_terms, #payment_plan_deposit, #payment_plan_adjustment", function(){
    var form = $(this).parents('form');

    var start_amount = parseFloat($(form).find('#payment_plan_outstanding').val());
    if ($(form).find('#payment_plan_deposit').val()) {
        start_amount -= parseFloat($(form).find('#payment_plan_deposit').val());
    }
    if ($(form).find('#payment_plan_adjustment').val()) {
        start_amount += parseFloat($(form).find('#payment_plan_adjustment').val());
    }
    $(form).find('#payment_plan_start_amount').val(start_amount.toFixed(2));

    calculate_payment_plan(form, function(response){
        var total = 0;
        for (var i in response) {
            total += response[i].total;
        }
        $(form).find("#payment_plan_total").val(total.toFixed(2));
    });
});

$(document).on("change", "#payment_plan_interest_type", function(){
    $("#payment_plan_interest_d .input-group-addon").html(this.options[this.selectedIndex].text);
});
function save_payment_plan(form, callback)
{

    var installments = [];
    if ($(form).find("#payment_plan_term_type").val() == "custom") {
        $(form).find("tr.installment").each (function(){
            installments.push(
                {
                    amount: $(this).find(".amount").val(),
                    interest: $(this).find(".interest").val(),
                    total: $(this).find(".total").val(),
                    due: $(this).find(".due").val()
                }
            );
        });
    }
    $.post(
        '/admin/payments/save_payment_plan',
        {
            transaction_id: $(form).find('#payment_plan_transaction_id').val(),
            amount: $(form).find('#payment_plan_outstanding').val(),
            deposit: $(form).find('#payment_plan_deposit').val(),
            adjustment: $(form).find('#payment_plan_adjustment').val(),
            terms: $(form).find('#payment_plan_terms').val(),
            term_type: $(form).find('#payment_plan_term_type').val(),
            interest_type: $(form).find('#payment_plan_interest_type').val(),
            interest: $(form).find('#payment_plan_interest').val(),
            starts: $(form).find('#payment_plan_starts').val(),
            installments: installments
        },
        function (response) {
            if (callback) {
                callback(response);
            }
        }
    );
}

$(document).on('click', '#make_payment_plan_modal_btn', function(e) {
    e.stopPropagation();
    e.preventDefault();

    var form = $(this).parents('#make_payment_plan_modal');
    save_payment_plan(form, function (response){
        $("#make_payment_plan_modal").modal('hide');
        $("a[href='#family-member-accounts-tab']").click();
        display_balance();
    });
});

function cancel_payment_plan(form, callback)
{
    $.post(
        '/admin/payments/cancel_payment_plan',
        {
            id: $(form).find('#cancel_payment_plan_modal_btn').data('id'),
        },
        function (response) {
            if (callback) {
                callback(response);
            }
        }
    );
}

$(document).on('click', '#cancel_payment_plan_modal_btn', function (e) {
    e.stopPropagation();
    e.preventDefault();

    var form = $(this).parents('#make_payment_plan_modal');
    cancel_payment_plan(form, function (response){
        $("#make_payment_plan_modal").modal('hide');
        $("a[href='#family-member-accounts-tab']").click();
        display_balance();
    });
});

$(document).on('click', '.make_payment_journal', function(ev) {
    ev.stopPropagation();
	ev.preventDefault();
    var contact;
    if ($(this).parent('#family-member-accounts-tab').length > 0)
    {
        contact = $('#contact_id').val();
    }
    else
    {
        contact = $('#primary_contact_id').val();
    }
    if (contact == '')
    {
        $('#no_primary_alert_modal').modal();
    }
    else
    {
        var data = { transaction_id : transaction_id,
            contact_id: $('#contact_id').val(),
            credit:0
        };
        make_payment_modal(data);
    }
});

function make_payment_modal(data)
{
    if (transaction_id == '' && data.transaction_id == '') {
        data.booking_id = 'all';
    } else {

    }
        $.ajax({
            url:'/admin/payments/ajax_show_payment_modal',
            data:data,
            datatype:'json',
            type:'POST'
        })
        .success(function(results){
                $('#make_payment_modal').remove();
                $('body').append(results);
                $('#make_payment_modal_btn').data('send_backend_booking_emails', data.send_backend_booking_emails);
                $('#make_payment_modal').modal();
                $('#make_payment_modal_type option[value="transfer"]').hide();
                var tran_type = $('#make_payment_modal_transaction_type').val();
                if (tran_type == 0)
                {
                    $('#modal_make_payment_status option[value="5"]').hide();
                }
                if ($('#modal_make_payment_family_available_balance').val() <= 0)
                {
                    $('#make_payment_family_balance_available').hide();
                }
                if ($('#modal_make_payment_contact_available_balance').val() <= 0)
                {
                    $('#make_payment_contact_balance_available').hide();
                    $(this).find('input:radio').attr('checked', true);
                }
                else
                {
                    $('#make_payment_contact_balance_available').find('input:radio').attr('checked', true);
                }
                if ($('#modal_make_payment_family_available_balance').val() <= 0 &&  $('#modal_make_payment_contact_available_balance').val() <= 0)
                {
                    $('#modal_make_payment_status option[value="5"]').hide();
                }
        });

}

$(document).on('click', '.edit_payment', function(ev){
    ev.stopPropagation();
    var payment_id = $(this).parent().data('payment_id'),
        modal_box = $('#make_payment_modal'),
        alerts = modal_box.find('.alert-area'),
        via = '',
        card_info = $('#card_payment_info'),
        cheque_info = $('#cheque_options_info');

    modal_box.css('width', '420px');
    card_info.css('display','none');
    cheque_info.css('display','none');
    $.ajax({
        url: '/admin/contacts3/ajax_get_payment?id=' + payment_id,
        dataType: 'json'
    })
    .success(function(result){
            //console.log(result);
        if(result.type == 'card')
        {
            via = 'via card ';
            modal_box.animate({width: '800px'}, {duration: 1000, complete: function(){
                $('#make_payment_modal_form').css('display', 'inline-flex');
                card_info.css('display','block');
            }});
        }
        if(result.type == 'card')
        {
            $('#make_payment_modal_type').val('card').trigger('change');
        }
        if(result.type == 'cheque')
        {
            via = 'via Cheque ';
            modal_box.animate({width: '800px'}, {duration: 1000, complete: function(){
                $('#make_payment_modal_form').css('display', 'inline-flex');
                cheque_info.css('display','block');
            }});
        }
        modal_box.find('h3').text('Save Payment ' + via + 'ID: ' + payment_id);
        $('#modal_make_payment_id').val(payment_id);
            //console.log(result.type);
        $('#make_payment_modal_type').val(result.type);
        $('#modal_make_payment_transaction_id').val(result.transaction_id);
        $('#modal_make_payment_amount').val(result.amount);
        $('#modal_make_payment_bank_fee').val(result.bank_fee);
        $('#modal_make_payment_status').val(result.status);
        $('#modal_make_payment_note').val(result.note);
        $('#modal_make_payment_cheque_record_name').val(result.name_cheque);
        calculateTotal(result.amount, result.bank_fee, $('#modal_make_payment_total_due'));
        modal_box.modal();
    })
    .error(function(){
        alerts.add_alert('The server did not respond. Please try again later.', 'danger popup_box');
        remove_popbox();
    });
});

$(document).on('click', '.delete_payment', function(ev){
    ev.stopPropagation();
    var payment_id = $(this).parent().data('payment_id'),
        alerts = $('#delete_modal .alert-area');
    $('#delete_modal h3').text('Delete Payment ID: ' + payment_id);
    $('#modal_delete_id').val(payment_id);
    $('#modal_controller').val('payments');
    $('#delete_modal').modal();
});

// common

$(document).on('click', '#delete_modal_btn', function()
{
    var id = $('#modal_delete_id').val(),
        alerts = $('#delete_transaction_modal').find('.alert-area'),
        controller = $('#modal_controller').val();
    $.ajax({
        url: '/admin/' + controller +'/ajax_delete?id=' + id,
        dataType: 'json'
    }).success(function(results)
    {
        if (results.status == 'success')
        {
            $('#delete_modal').find('.close').click();
            $('[href="#family-member-accounts-tab"]').click();
        }
        else
        {
            alerts.add_alert(results.message);
        }
    }).error(function()
    {
        alerts.add_alert('The server did not respond. Please try again later.', 'danger popup_box');
        remove_popbox();
    });
});

$(document).on('keyup','#modal_make_payment_bank_fee,#modal_make_payment_amount', function(){
    var fee =  parseFloat($('#modal_make_payment_bank_fee').val()),
        amount = parseFloat($('#modal_make_payment_amount').val()),
        total = $('#modal_make_payment_total_due');
    calculateTotal(amount, fee, total);

});

function calculateTotal(amount, fee, total_input)
{
    amount = parseFloat(amount);
    fee    = parseFloat(fee);
    var total = fee + amount;
    if (isNaN(total))
    {
        total = fee || amount;
    }
    if (isNaN(total))
    {
        total = 0.00;
    }
    total_input.val(total);

    var btotal_left = amount;
    var bratios = [];
    var outstanding = parseFloat($("#modal_make_payment_outstanding").val());
    /*var $btransactions = $(".btransaction_payment_amount");
    for (var i = 0; i < $btransactions.length; ++i) {
        var boutstanding = parseFloat($($btransactions[i]).data("outstanding"));
        bratios[i] = boutstanding / outstanding;
    }
    if (outstanding > btotal_left) {
        for (var i = 0; i < $btransactions.length; ++i) {
            var boutstanding = parseFloat($($btransactions[i]).data("outstanding"));
            var bshare = Math.ceil(amount * bratios[i]);

            var autofill = Math.min(bshare, boutstanding);
            autofill = Math.min(autofill, btotal_left);
            $($btransactions[i]).val(autofill);
            btotal_left -= autofill;
        }
    } else {
        for (var i = 0; i < $btransactions.length; ++i) {
            var boutstanding = parseFloat($($btransactions[i]).data("outstanding"));
            var autofill = Math.min(boutstanding, btotal_left);
            $($btransactions[i]).val(autofill);
            btotal_left -= autofill;
        }
    }*/
}

$(document).on('change','#modal_make_transaction_billed', function(){
    var modal = $('#make_transaction_modal.modal'),
        payer_info = $('#bill_payer_information');

    if ($('#modal_make_transaction_billed :checkbox:selected'))
    {
        modal.animate({width:'800px'}, 1000);
        payer_info.css('display','block');
        $('#madal_make_transaction_bill_payer_id').val(436);
    }
    else
    {
        $('#madal_make_transaction_bill_payer_id').val('');
        payer_info.css('display','none');
    }
});

$('#modal_make_transaction_bill_payer_name').autocomplete({
    source:function(request, response){
        $.getJSON("admin/contacts3/find_contact",{term:getElementById('modal_make_transaction_bill_payer_name').value},response);
    },
    minLength: 2,
    select: function(event, ui){
        $('#madal_make_transaction_bill_payer_id').val(ui.item.id);
    },
    messages: {
        noResults:'',
        results: function() {}
    }
});

function load_payment(transaction_id,transaction_balance)
{

    $('.payments_data').load('/admin/bookingstransactions/ajax_get_payments?transaction_id=' + transaction_id, function() {
        $(this).find('.dataTable').attr('id', 'family_member_payment_table').dataTable({"aaSorting": []});
        $('#modal_make_payment_transaction_id').val(transaction_id);
        $('#modal_make_payment_transaction_balance').val(transaction_balance);
        $('#make_payment_modal .modal-header h3').text('Make Payment (Transaction id: ' + transaction_id + ')');
        $('#family_member_payment_table .popinit').popover({placement:'top',trigger:'hover'});
    });
}

/**
 * Cancel booking
 */

$(document).on('click','.journal_cancel_transaction', function(ev) {
    ev.stopPropagation();
	ev.preventDefault();
    if (transaction_id == '')
    {
        $('#alert_modal').modal();
    }
    else
    {
        show_cancel_booking_modal({transaction_id:transaction_id});
    }
});

$(document).on('click','#cancel_booking_modal_btn', function() {
    var data = {
        contact_id:$('#contact_id').val(),
        transaction_id:$('#cancel_booking_modal_transaction_id').val(),
        booking_id:$('#cancel_booking_modal_booking_id').val(),
        credit_amount:$('#cancel_booking_modal_credit_amount').val(),
        transaction_balance:$('#cancel_booking_modal_transaction_balance').val(),
        credit_payment:document.querySelector('input[name="credit_transaction"]:checked').value,
        credit_destination:document.querySelector('input[name="journal_type"]:checked').value,
        note:$('#cancel_booking_modal_note').val(),
        credit_to_family_id:document.querySelector('input[name="credit_to_family_id"]').value,
        credit_to_contact_id:document.querySelector('input[name="credit_to_contact_id"]').value,
    };
    cancel_booking(data);
});

$(document).on('change','input:radio[name="credit_transaction"]', function(){
    if($(this).val() == 'yes'){
        $("#cancel_booking_modal_credit_amount").removeAttr("readonly");
    }
    if($(this).val() == 'no'){
        $("#cancel_booking_modal_credit_amount").attr("readonly", true);
        $('cancel_booking_modal_credit_amount').val(0.00);
        $('#cancel_booking_modal_transaction_balance').val(0.00);
    }
});


$(document).on('keyup','#cancel_booking_modal_credit_amount', function(){
    var amount = parseFloat($('#cancel_booking_modal_payed').val()),
        credit_amount =  parseFloat($('#cancel_booking_modal_credit_amount').val()),
        total;
    if ( amount >= credit_amount)
    {
        total = amount - credit_amount;
    }
    else
    {
        total = amount;
        $('#cancel_booking_modal_credit_amount').val(0.00);
    }
    $('#cancel_booking_modal_transaction_balance').val(total);
});

function show_cancel_booking_modal(data)
{
    $.ajax({
        url:'/admin/bookingstransactions/ajax_show_cancel_booking',
        data:data,
        datatype:'json',
        type:'POST'
    })
        .success(function(results){
            $('#cancel_booking_modal').remove();
            $('body').append(results);
            $('#cancel_booking_modal').modal();

            $("#cancel_booking_modal #credit_to_family_autocomplete").on("change", function(){
                if (this.value == "") {
                    $('[name=credit_to_family_id]').val('');
                }
            });
            $("#cancel_booking_modal #credit_to_family_autocomplete").autocomplete({
                source: function(data, callback){
                    $('[name=credit_to_family_id]').val('');
                    $.get("/admin/contacts3/ajax_get_all_families_ui",
                        data,
                        function(response){
                            callback(response);
                        });
                },
                open: function () {
                    $('[name=credit_to_family_id]').val('');
                },
                select: function (event, ui) {
                    $('[name=credit_to_family_id]').val(ui.item.id);
                }
            });

            $("#cancel_booking_modal #credit_to_contact_autocomplete").on("change", function(){
                if (this.value == "") {
                    $('[name=credit_to_contact_id]').val('');
                }
            });
            $("#cancel_booking_modal #credit_to_contact_autocomplete").autocomplete({
                source: function(data, callback){
                    $('[name=credit_to_contact_id]').val('');
                    $.get("/admin/contacts3/ajax_get_all_contacts_ui",
                        data,
                        function(response){
                            callback(response);
                        });
                },
                open: function () {
                    $('[name=credit_to_contact_id]').val('');
                },
                select: function (event, ui) {
                    $('[name=credit_to_contact_id]').val(ui.item.id);
                }
            });
        });
}

function cancel_booking(data)
{
    var alerts = $('#cancel_booking_modal .alert-area');
    var balance_data = {contact_id:$('#contact_id').val(),family_id:null};
    $.ajax({
        type:'POST',
        url:'/admin/bookingstransactions/ajax_cancel_transaction',
        data:data,
        dataType:'json'
    })
        .success(function(results)
        {
            //results = JSON.parse(results);
            if(results.status == 'success')
            {
                display_balance(
                    balance_data,
                    function() {
                        var table = 'member';
                        if(alerts.parents('#family-accounts-tab').length){
                            table = 'family';
                        }
                        $('#cancel_booking_modal').modal('hide');
                        if ($('#family-member-bookings-tab').is(':visible'))
                        {
                            $('[href="#family-member-bookings-tab"]').click();
                        }
                        else if ($('#family-accounts-tab').is(':visible'))
                        {
                            $('[href="#family-accounts-tab"]').click();
                        }
                        else if ($('#family-member-accounts-tab').is(':visible'))
                        {
                            $('[href="#family-member-accounts-tab"]').click();
                        }
                    }
                );
                alerts.add_alert(results.message,'success');
            }
            else
            {
                alerts.add_alert(results.message);
            }
        })
        .error(function()
        {
            alerts.add_alert('The server did not respond. Please try again later.', 'danger popup_box');
            remove_popbox();
        });
}

/**
 * Display Balance
 */
function display_balance(data, callback)
{
    if (typeof data == 'undefined')
    {
		var family_id = null;
		if (document.getElementById('contact_family_id'))
		{
			family_id = document.getElementById('contact_family_id').value;
		}
		else if (document.getElementById('family_id'))
		{
			family_id = document.getElementById('family_id').value;
		}
    }
    var contact;
    if ($(this).parent('#family-accounts-tab').length)
    {
        contact = $('#primary_contact_id').val();
    }
    else
    {
        contact = $('#contact_id').val();
    }
    $.ajax({
        url:'/admin/bookingstransactions/ajax_show_contact_balance',
        data:{
			contact_id: contact,
			family_id: family_id
		},
        datatype:'json',
        type:'POST'
    })
        .success(function(results) {
            if (results) {
                results = JSON.parse(results);
                render_tx_balances(results);
                if (callback) {
                    callback();
                }
            }
        });
}

function render_tx_balances(balance)
{
    $('#family_balance_status').html(balance ? balance.family_balance  : '');
    $('#member_balance_status').html(balance ? balance.contact_balance : '');
    $('#member_balance_status').find('.popinit').popover({placement: 'bottom', trigger: 'hover', html: true});
    $('#family_balance_status').find('.popinit').popover({placement: 'bottom', trigger: 'hover', html: true});
}

$(document).on("click", ".make_booking_payment", function(){
    var contact_id = $("#contact_id").val();

        $.ajax({
            url:'/admin/payments/ajax_show_payment_modal',
            data: {contact_id: contact_id, booking_id: "all", credit: 1},
            datatype:'json',
            type:'POST'
        })
        .success(function(results){
            $('#make_payment_modal').remove();
            $('body').append(results);
            $('#make_payment_modal_type option[value="transfer"]').hide();
            var tran_type = $('#make_payment_modal_transaction_type').val();
            if (tran_type == 0)
            {
                $('#modal_make_payment_status option[value="5"]').hide();
            }
            if ($('#modal_make_payment_family_available_balance').val() <= 0)
            {
                $('#make_payment_family_balance_available').hide();
            }
            if ($('#modal_make_payment_contact_available_balance').val() <= 0)
            {
                $('#make_payment_contact_balance_available').hide();
                $(this).find('input:radio').attr('checked', true);
            }
            else
            {
                $('#make_payment_contact_balance_available').find('input:radio').attr('checked', true);
            }
            if ($('#modal_make_payment_family_available_balance').val() <= 0 &&  $('#modal_make_payment_contact_available_balance').val() <= 0)
            {
                $('#modal_make_payment_status option[value="5"]').hide();
            }
            $('#make_payment_modal').modal();

        });

    return false;
});

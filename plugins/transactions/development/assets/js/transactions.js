var transaction_contact_editor = {
    selected_transaction_id: null,

    setup: function($container) {
        transactions_list_setup($container);
        $container.find(".table.transactions").dataTable();
        $container.find(".table.payments").dataTable();
    },

    validate: function(container) {

    }
};

if (window.contact_editor) {
    window.contact_editor.extensions.push(transaction_contact_editor);
}

function load_transaction($container, transaction_id)
{
    transaction_contact_editor.selected_transaction_id = transaction_id
        var transaction = null;
        $.ajax(
            '/admin/transactions/get_transaction_data',
            {
                method: 'POST',
                data: {
                    transaction_id: transaction_contact_editor.selected_transaction_id
                },
                success: function (response) {
                    if (response.transaction) {
                        transaction = response.transaction;
                        var $payment = $container.find(".make_payment.modal");
                        $payment.find(".transaction_id").html(transaction.id);
                        $payment.find("[name=transaction_id]").val(transaction.id);
                        $payment.find("input.outstanding").val(transaction.outstanding);
                        $payment.find("[name=transaction_updated]").val(transaction.transaction_updated);
                        $payment.find("[name=amount]").val(transaction.outstanding);
                        $payment.modal('show');
                    }
                },
                error: function() {

                }
            }
        );
}

function transactions_list_setup($container)
{
    $container.find(".table.transactions tbody").on("click", function(e){
        var tr = $(e.target).parents("tr");
        var transaction_id = $(tr).data("id");
        var updated = $(tr).find(">td:nth-child(6)").html();
        load_payments(transaction_id, updated);
    });

    $container.find(".add.payment").on("click", function(){
        if (transaction_contact_editor.selected_transaction_id == null) {
            $(this).parents(".tab-pane").find(".alert_no_selected_transaction.modal").modal("show");
        } else {
            load_transaction($(this).parents(".tab-pane"), transaction_contact_editor.selected_transaction_id);
        }
        return false;
    });

    var $makepayment = $container.find(".make_payment.modal");

    $makepayment.find("select.gateway").on(
        "change",
        function () {
            $container.find(".make_payment_modal_column.gateway").hide();
            if (this.selectedIndex > 0) {
                $container.find(".make_payment_modal_column.gateway." + this.value).show();
            }
        }
    );

    $makepayment.find(".btn.save").on(
        "click",
        function () {
            var transaction_id = $makepayment.find("[name=transaction_id]").val();
            //var validated = $makepayment.find("form#make_payment_modal_form").validationEngine('validate');
            var validated = true;
            if (validated = true){
                //test if transaction has not been updated by someone or something else
                $.ajax(
                    '/admin/transactions/get_transaction_data',
                    {
                        method: 'POST',
                        data: {
                            transaction_id: transaction_id
                        },
                        success: function (response) {
                            if (response.changed) {
                                $makepayment.find("form.make_payment").reset();
                                $makepayment.hide();
                                $(".alert_changed_transaction.modal").modal("show");
                            } else {
                                $.ajax(
                                    '/admin/transactions/make_payment',
                                    {
                                        method: 'POST',
                                        data: $makepayment.find("form.make_payment").serialize(),
                                        success: function (response) {
                                            if (response.payment.status == 'Completed') {
                                                $makepayment.modal('hide');
                                                try { // on contacts screen reload contact form
                                                    load_contact(
                                                        $("#form_add_edit_contact [name=id]").val(),
                                                        null,
                                                        null,
                                                        function () {
                                                            $("[href='#contact-extention-accounts-tab']").click();
                                                            $(".table.transactions tbody tr[data-id=" + transaction_id + "]").click();
                                                        }
                                                    );
                                                } catch (exc) { // not on contacts screen. refresh same page
                                                    window.location.reload();
                                                }
                                            } else if (response.payment.status == 'Processing') {
                                                alert("not implemented")
                                            } else {
                                                $(".alert_payment_failed.modal")
                                                    .find(".modal-body p")
                                                    .html(response.payment.message);
                                                $(".alert_payment_failed.modal").modal("show");
                                            }
                                        },
                                        error: function() {
                                            $(".alert_payment_failed.modal")
                                                .find(".modal-body p")
                                                .html("Unknown error");
                                            $(".alert_payment_failed.modal").modal("show");
                                        }
                                    }
                                );
                            }
                        },
                        error: function() {

                        }
                    }
                );
            }
        }
    );
}

function load_payments(transaction_id, updated, callback)
{
    transaction_contact_editor.selected_transaction_id = transaction_id;
    var url = "/admin/transactions/get_payments";
    var data = {};
    if (parseInt(transaction_id)) {
        data.transaction_id = transaction_id;
    }
    if (updated) {
        data.updated = updated;
    }

    $.ajax(
        url,
        {
            method: 'GET',
            cache: updated ? true : false,
            data: data,
            success: function (response) {
                $tbody = $(".table.payments tbody");
                $tbody.html("");

                var trs = "";
                for (var i = 0 ; i < response.payments.length ; ++i) {
                    trs +=
                        '<tr>' +
                            '<td>' + response.payments[i].id + '</td>' +
                            '<td>' + response.payments[i].to_transaction_id + '</td>' +
                            '<td>' + response.payments[i].payment_type + '</td>' +
                            '<td>' + response.payments[i].gateway + '</td>' +
                            '<td>' + response.payments[i].amount + '</td>' +
                            '<td>' + response.payments[i].status + '</td>' +
                            '<td>' +
                                '<span class="popinit" data-original-title="Payment Notes" data-placement="left" rel="popover" data-content="' + (response.payments[i]['note'] != null ? response.payments[i]['note'] : '') + '"><i class="icon-book"></i></span>' +
                            '</td>' +
                            '<td>' + response.payments[i].updated + '</td>' +
                        '</tr>';
                }

                $tbody.html(trs);

                $tbody.find(".popinit").popover({placement:'top',trigger:'hover'});

                try {
                    $tbody.parent()[0].scrollIntoView();
                } catch (exc) {

                }

                if (callback) {
                    callback(response);
                }
            },
            error: function() {

            }
        }
    );
}

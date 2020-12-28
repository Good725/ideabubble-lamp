$(document).on("ready", function(){

    // Avoiding dependency on PAC-281. This can be removed after its updates are safely on the same branch as this code.
    if (typeof clean_date_string !== 'function') {
        function clean_date_string(date){ //Add "T" to date strings for Safari browsers
            return (date.match(/(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}(:\d{2})?)/) ? date.replace(/\s/, "T") : date);
        }
    }

    function clear_request_form()
    {
        var purchase_order_model = $("#purchasing-request-modal");
        purchase_order_model.find("input").val("");
        $("#po-request-products-wrapper").find(".po-request-product-item").remove();
        purchase_order_model.find(".modal-header .modal-title").text("Request a purchase order");
        purchase_order_model.find("#purchasing-request-form > .form-row.vertically_center > h1").text("Request a PO");
        purchase_order_model.find("#purchasing-request-form > .form-row.vertically_center > .icon-plus-circle").show();
        purchase_order_model.find(".modal-footer .btn").hide();
    }

    $("#new-purchasing-request").on("click", function(){
        $("#purchasing-request-modal").modal();
        clear_request_form();
        $("#purchasing-request-modal").find(".modal-footer .btn.request").show();
        add_product();
    });

    function add_product()
    {
        var $product = $("#po-request-product-item-template").clone();
        var index = $(".po-request-product-item").length;

        // Give each item unique IDs and field name indexes
        $product.find("input, select").each(function() {
            this.name = this.name.replace('[index]', '[' + index + ']');
        });
        $product.find("[for]").each(function() {
            this.setAttribute('for', this.getAttribute('for').replace('index', index));
        });
        $product.find("[id]" ).each(function() {
            this.id  = this.id.replace('index', index);
        });

        $product.removeAttr('id').removeClass('hidden');
        $product.find('[id^=po-request-product-item-].form-input.product').first().autocomplete({
            source: function (data, callback) {
                $.get("/admin/inventory/ajax_get_inventory_items_autocomplete",
                    data,
                    function (response) {
                        callback(response);
                    });
                },
            open: function () {
                $('[name=credit_to_family_id]').val('');
            },
            select: function (event, ui) {
                var selected_row = $(this).closest(".po-request-product-item");
                // selected_row.find(".product-id").val(ui.item.id);
                var unit_type = selected_row.find(".po-request-product-item-amount_type").first();
                var item_vat_rate = ui.item.vat_rate || 0;
                $(this).data("vat-rate", item_vat_rate);
                var amount_type = ui.item.amount_type || "Unit";
                unit_type.val(amount_type);
                unit_type.trigger("change");
                $product.find('.inventory_item_id').val(ui.item.id);
            }
        });

        $("#po-request-products-wrapper").append($product);
    }

    function open_purchase_order(purchase_order_id)
    {
        var purchase_order_model = $("#purchasing-request-modal");
        clear_request_form();
       $.get("/admin/purchasing/ajax_get_purchasing_order_information",
            {"purchase_order_id": purchase_order_id},
            function (response) {
                $(".modal-footer button.btn").data("id", response.id);
                purchase_order_model.modal();
                purchase_order_model.find(".modal-header .modal-title").text("View a purchase order");
                $('#purchasing-request-status-label').addClass('hidden').html('');

                if (response.status == "Approved" || response.status == "Purchased") {
                    purchase_order_model.find("#purchasing-request-form > .form-row.vertically_center > h1")
                        .html('<span style="font-weight: normal;">PO Number <strong style="font-weight: bolder;">' + response.id+ '</strong></span>');
                    $('#purchasing-request-status-label').removeClass('hidden').text(response.status.toUpperCase());
                    purchase_order_model.find("#purchasing-request-form > .form-row.vertically_center > .icon-plus-circle").hide();
                } else {
                    purchase_order_model.find("#purchasing-request-form > .form-row.vertically_center > h1").text("View purchase order");
                }
                purchase_order_model.find(".btn.save").show();

                if (response.status == "Pending"){
                    purchase_order_model.find(".btn.approve").show();
                    purchase_order_model.find(".btn.decline").show();
                }

                purchase_order_model.find("#po-request-department").val(response.department_id).change();
                purchase_order_model.find('#po-request-supplier').val(response.supplier_id).change();
                purchase_order_model.find("#po-request-product-reviewer").val(response.reviewer_id).change();
                purchase_order_model.find('[name="date_required"]').val(response.date_required);
                purchase_order_model.find("#po-request-product-date_required").val(moment(response.date_required, "YYYY-MM-DD").format("DD/MMM/YYYY"));
                purchase_order_model.find("#purchasing-request-form input[name=id]").val(response.id);
                purchase_order_model.find("#purchasing-request-form #po-request-po_comment").val(response.comment || '');

                for (var i = 0; i < response.products.length; i++) {
                    $('#po-request-product-add').click();
                    var new_item_row = $('#po-request-products-wrapper').find('.po-request-product-item:last');
                    new_item_row.find('.product').val(response.products[i].product);
                    new_item_row.find('.product').val(response.products[i].product);

                    new_item_row.find('input[name="product[' + (i + 1) + '][id]"]').val(response.products[i].id);
                    new_item_row.find('.po-request-product-item-amount_type').val(response.products[i].amount_type || 'Unit').change();
                    new_item_row.find('.po-request-product-price').val(response.products[i].amount_price || 0);
                    new_item_row.find('.po-request-product-amount').val(response.products[i].amount || 0).change();
                    new_item_row.find('.inventory_item_id').val(response.products[i].inventory_item_id);
                }
            });
    }

    function update_purchase_order_status(action, id)
    {
        // Make the update serverside
        $.post('/admin/purchasing/' + action, {id: id}).done(function (data) {
            // Display message saying if the update worked
            $('#purchasing-alert_area').add_alert(data.message, (data.success ? 'success' : 'error') + ' popup_box');

            // If successful, refresh the table
            if (data.success) {
                update_list();
            }
        });
    }

    function save_purchase_order()
    {
        var $form = $("#purchasing-request-form");
        var data  = $form.serialize();

        if ($form.validationEngine('validate')) {
            $.post(
                "/admin/purchasing/request_save",
                data,
                function (response) {
                    $("#purchasing-request-modal").modal('hide');
                    $('#purchasing-alert_area').add_alert(response.message, (response.success ? 'success' : 'error') + ' popup_box');
                    update_list();
                }
            );
        }
    }
    $("#po-request-product-add").on("click", add_product);

    $("#po-request-products-wrapper").on("change", ".po-request-product-item-amount_type", function() {
        var $selected = $(this).find(":selected");
        var $item = $(this).parents(".po-request-product-item");

        $item.find(".po-request-product-amount-icon").html('<span title="'+$selected.attr('data-unit_name')+'">'+$selected.attr('data-unit')+'</span>');
        $item.find(".po-request-product-price-label").html($selected.attr('data-price_per_text'));
    });

    // Remove
    $("#po-request-products-wrapper").on("click", ".po-request-product-item-remove", function() {
        $(this).parents(".po-request-product-item").remove();
    });

    // Calculate the total price, when the price of an individual item or the VAT rate is changed
    $("#purchasing-request-form").on("change", ".po-request-product-price, .po-request-product-amount", function() {
        var total = 0;
        var total_with_vat = 0;
        var price;
        var amount;

        $("#po-request-products-wrapper").find(".po-request-product-item").each(function() {
            var selected_row = $(this).closest(".po-request-product-item");
            var vat_row_percentage = selected_row.find(".form-input.product.ui-autocomplete-input").data("vatRate") || 0;
            price  = parseFloat($(this).find(".po-request-product-price").val()  || 0);
            amount = parseFloat($(this).find(".po-request-product-amount").val() || 0);

            $(this).find(".po-request-product-line_total").val((price * amount).toFixed(2));

            total += price * amount;
            total_with_vat += (price + (price * (vat_row_percentage/100))) * amount;
        });

        document.getElementById("po-request-product-total").value = total.toFixed(2);
        document.getElementById("po-request-product-total_with_vat").value = total_with_vat;
    });

    $("#purchasing-request-modal .btn.request, #purchasing-request-modal .btn.save").on("click", function(){
        save_purchase_order();
    });

    function list_requests(extra_params)
    {
        var id = '#purchasing-purchases-table';
        var start_date = null;
        var end_date = null;
        try {
            start_date = $('#purchasing-period-start_date').val();
            end_date = $('#purchasing-period-end_date').val();
        } catch (exc) {
            console.log(exc);
        }
        var $table = $(id);
        var status = $("#purchasing_status").val();

        $table.ib_serverSideTable(
            '/admin/purchasing/requests',
            {
                aaSorting: [[ 5, 'desc']],
                sServerMethod: "POST",
                fnServerParams: function (params) {
                    params.push({name: "status", value: status});
                    params.push({name: "after", value: start_date});
                    params.push({name: "before", value: end_date});

                    if (extra_params) {
                        for (param in extra_params) {
                            params.push({name: param, value: extra_params[param]});
                        }
                    }


                }
            },
            {
                responsive: true,
                //row_data_ids: true,
                draw_callback: function() {
                    var status;
                    $table.find('.timetable-planner-timeslot-status').each(function() {
                        status = $(this).data('status');
                        $(this).parents('td').attr('data-status', status).data('status', status);
                    });

                    var has_rows = ($table.find('tbody tr').length > 0 && $table.find('.dataTables_empty').length == 0);
                    var is_filtered = $('#purchasing-purchases-table_filter').find('input').val();
                    var is_empty_without_filter = !is_filtered && !has_rows;

                    $('#purchasing-purchases-table-wrapper').toggleClass('hidden', is_empty_without_filter);
                    $('#purchasing-purchases-table-empty').toggleClass('hidden', !is_empty_without_filter);
                }
            }
        );
    }

    function update_overview()
    {
        var params = {};
        params.before = null;
        params.after = null;
        try {
            params.after = $('#purchasing-period-start_date').val();
            params.before = $('#purchasing-period-end_date').val();
        } catch (exc) {

        }
        if ($("#purchasing_status").val()) {
            params.status = $("#purchasing_status").val().join(',');
        }

        $.post(
            '/admin/purchasing/overview',
            params,
            function(response) {
                var $table = $("#purchasing-overview-table-wrapper");
                var thead = '<thead>';
                thead += '<tr>' +
                        '<th>Department</th>' +
                        '<th>Count</th>' +
                        '<th>€Value</th>';
                for (var month in response.total_spent_months) {
                    thead += '<th data-month = "' + month + '">' + month + '</th>';
                }
                thead += '</tr>';
                thead += '</thead>';

                var tbody = '<tbody>';
                for (var i in response.department) {
                    tbody += '<tr data-department_id="' + response.department[i].department_id + '">' +
                            '<td>' + response.department[i].department + '</td>' +
                        '<td>' + response.department[i].count + '</td>' +
                        '<td>' + response.department[i].value.toFixed(2) + '</td>';
                    for (var month in response.total_spent_months) {
                        tbody += '<td data-month = "' + month + '">' + (response.department[i].months[month] != null ? response.department[i].months[month].toFixed(2) : '0') + '</td>';
                    }
                    tbody += '</tr>';
                }
                tbody += '</tbody>';

                var tfoot = '<tfoot>';
                tfoot += '<tr>' +
                    '<th>Total</th>' +
                    '<th>' + response.total.count + '</th>' +
                '<th>' + response.total.value.toFixed(2) + '</th>';
                    for (var month in response.total_spent_months) {
                        tfoot += '<th>' + (response.total.months != null && response.total.months[month] != null ? response.total.months[month].toFixed(2) : '0') + '</th>';
                    }
                tfoot += '</tr>';
                tfoot += '</tfoot>';
                $table.html(thead + tbody + tfoot);
                var has_rows = ($table.find('tbody tr').length > 0 && $table.find('.dataTables_empty').length == 0);
                // If it has rows hide the no records notification
                $('#purchasing-purchases-table-empty').toggleClass('hidden', has_rows);
            }
        )
    }

    function update_list()
    {
        if ($("[name=purchasing-view]:checked").val() == "overview") {
            $("#purchasing-overview-table-wrapper").addClass("hidden");
            $("#purchasing-purchases-table-wrapper").removeClass("hidden");
            list_requests();
        } else {
            $("#purchasing-purchases-table-wrapper").addClass("hidden");
            $("#purchasing-overview-table-wrapper").removeClass("hidden");
            update_overview();
        }
    }
    function trigger_full_year() {
        var start_date = (new Date()).getFullYear() + "-01-01";
        var end_date = (new Date()).getFullYear() + "-12-31";
        $('#purchasing-period-start_date').val(start_date);
        $('#purchasing-period-end_date').val(end_date);
        $('.form-daterangepicker-input').data('daterangepicker').setStartDate(moment(start_date, "YYYY-MM-DD"));
        $('.form-daterangepicker-input').data('daterangepicker').setEndDate(moment(end_date, "YYYY-MM-DD"));
    }
    // Change a status (approved, declined, purchased)
    $(document).on('click', '.purchasing-purchases-change_status', function() {
        const action = $(this).data('action');
        const id = $(this).data('id');
        update_purchase_order_status(action, id);
        $("#purchasing-request-modal").modal('hide')
    });

    $('#purchasing-purchases-table').on("click", '.view',function () {
        const id = $(this).data('id');
        open_purchase_order(id);
    });

    $("#purchasing_status, #purchasing-period").on("change", function(){
        update_list();
    });

    $("[name=purchasing-view]").on("change", function(){
        // Don't trigger update again as start/end date triggers on change event
        if (this.value == "details") {
            trigger_full_year();
        } else {
            update_list();
        }
    });
    $("#purchasing-overview-table-wrapper").on("click", "td, th", function(){
        var month = $(this).data("month");
        var department_id = $(this).parents("tr").data("department_id");
        if (department_id || month) {
            $("#purchasing-purchases-table-wrapper").removeClass("hidden");
            list_requests({month: month, department_id: department_id});
        }
    });
    update_list();
});

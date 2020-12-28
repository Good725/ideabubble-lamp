var amount_types = {
    Volume: "lt",
    Weight: "kg",
    Unit: "qty"
};

var amount_labels = {
    Volume: "Litres",
    Weight: "Kilograms",
    Unit: "Quantity"
};

function list_stocks()
{
    var $table       = $("#stock-table");
    var url_query    = new URLSearchParams(window.location.search);
    var location_ids = $('#purchasing-filter-locations').val() || [];
    var item_ids     = $('#purchasing-filter-items').val()     || [];
    var i;

    $table.ib_serverSideTable(
        '/admin/inventory/stock_list',
        {
            aaSorting: [[ 5, 'desc']],
            sServerMethod: "POST",
            fnServerParams: function (params) {
                params.push({ name: "status",          value: $("#stock_status").val()            });
                params.push({ name: "after",           value: $("#stock-period-start_date").val() });
                params.push({ name: "before",          value: $("#stock-period-end_date").val()   });
                params.push({ name: "requested_by_id", value: url_query.get('contact_id')         });

                for (i = 0; i < location_ids.length; i++) {
                    params.push({ name: "location_ids[]", value: location_ids[i] });
                }

                for (i = 0; i < item_ids.length; i++) {
                    params.push({ name: "item_ids[]",  value: item_ids[i] });
                }
            }
        },
        {
            responsive: true,
            //row_data_ids: true,
            ajax_callback: function(data) {
                if (data.reports) {
                    var $reports = $("#inventory-stats").find('.timeoff-report');
                    var i = 0;
                    var date_range = $('#stock-period').val();

                    data.reports.forEach(function(report) {
                        var $report = $reports.eq(i);
                        $report.find('.timeoff-report-title').text(report.text);
                        $report.find('.timeoff-report-amount').html(report.amount);
                        $report.find('.timeoff-report-period').text(date_range);
                        i++;
                    });

                    var empty = (data.iTotalRecords == 0);
                    $('#stock-table-wrapper').toggleClass('hidden', empty);
                    $('#stock-table-empty').toggleClass('hidden', !empty);
                }
            }
        }
    );
}

function edit_stock(stock)
{
    var $modal = $("#stock-modal");
    $("#stock-modal input").val("");
    if (stock) {
        $modal.find("[name=id]").val(stock.id);
        $modal.find("[name=purchasing_item]").val(stock.purchasing_item);
        $modal.find("[name=purchasing_item_id]").val(stock.purchasing_item_id);
        $modal.find("[name=stock-supplier]").val(stock.supplier);
        $modal.find("[name=supplier_id]").val(stock.supplier_id);
        $modal.find("[name=stock-item]").val(stock.item);
        $modal.find("[name=item_id]").val(stock.item_id);
        $modal.find("[name=amount_type]").val(stock.amount_type);
        $modal.find("[name=amount]").val(stock.amount);
        $modal.find("#expiry_date-iso").val(stock.expiry_date).change();
        $modal.find("[name=stock-location]").val(stock.location);
        $modal.find("[name=location_id]").val(stock.location_id);
    }
    $modal.modal();
}

function edit_checkin(checkout, checkin)
{
    var $modal = $("#checkin-modal");
    $("#checkin-modal input").val("");
    if (checkout) {
        $("#checkin-stock").val(checkout.item);
        $("#checkin-stock_id").val(checkout.stock_id);
        $modal.find("[name=checkout_id]").val(checkout.id);
        $modal.find("[name=amount_type]").val(checkout.amount_type);
        $modal.find("[name=amount]").val(checkout.available);
        $modal.find(".po-request-product-amount-icon").html(amount_types[checkout.amount_type]);
        $modal.find(".po-request-product-amount-icon").attr("title", amount_labels[checkout.amount]);
        $modal.find("[name=checkin-requestee]").val(checkout.requestee);
        $modal.find("[name=requestee_id]").val(checkout.requestee_id);
        $modal.find("[name=checkin-location]").val(checkout.location);
        $modal.find("[name=location_id]").val(checkout.location_id);
        $modal.find("#checkin-available").html(checkout.available + " / " + checkout.amount);
    }
    if (checkin) {
        $modal.find("[name=id]").val(checkin.id);
        $modal.find("[name=checkout_id]").val(checkin.checkout_id);
        $("#checkin-stock").val(checkin.item);
        $("#checkin-stock_id").val(checkin.stock_id);
        $modal.find("[name=amount_type]").val(checkin.amount_type);
        $modal.find("[name=amount]").val(checkin.amount);
        $modal.find(".po-request-product-amount-icon").html(amount_types[checkin.amount_type]);
        $modal.find(".po-request-product-amount-icon").attr("title", amount_labels[checkin.amount]);
        $modal.find("[name=checkin-location]").val(checkin.location);
        $modal.find("[name=location_id]").val(checkin.location_id);
        $modal.find("[name=checkin-requestee]").val(checkin.requestee);
        $modal.find("[name=requestee_id]").val(checkin.requestee_id);
        $modal.find("#checkin-available").html(checkin.checkout_available + " / " + checkin.checkout_amount);
    }
    $("#checkin-amount_type").change();
    $modal.modal();
}

function edit_checkout(stock, checkout)
{
    var $modal = $("#checkout-modal");
    $("#checkout-modal input").val("");
    if (stock) {
        $modal.find("[name=stock_id]").val(stock.id);
        $("#checkout-stock").val(stock.item);
        $("#checkout-stock_id").val(stock.id);
        $modal.find("[name=amount_type]").val(stock.amount_type);
        $modal.find("[name=amount]").val(stock.available);
        $modal.find(".po-request-product-amount-icon").html(amount_types[stock.amount_type]);
        $modal.find(".po-request-product-amount-icon").attr("title", amount_labels[stock.amount]);
        $modal.find("#checkout-available").html(stock.available + " / " + stock.amount);
    }
    if (checkout) {
        $modal.find("[name=id]").val(checkout.id);
        $modal.find("[name=checkout-requestee]").val(checkout.requestee);
        $modal.find("[name=requestee_id]").val(checkout.requestee_id);
        $("#checkout-stock").val(checkout.item);
        $("#checkout-stock_id").val(checkout.stock_id);
        $modal.find("[name=amount_type]").val(checkout.amount_type);
        $modal.find("[name=amount]").val(checkout.amount);
        $modal.find(".po-request-product-amount-icon").html(amount_types[checkout.amount_type]);
        $modal.find(".po-request-product-amount-icon").attr("title", amount_labels[checkout.amount]);
        $modal.find("[name=checkout-location]").val(checkout.location);
        $modal.find("[name=location_id]").val(checkout.location_id);
        $modal.find("#checkout-available").html(checkout.stock_available + " / " + checkout.stock_amount);
    }
    $("#checkout-amount_type").change();
    $modal.modal();
}

function autocomplete_set(name, url)
{
    var last_id = null;
    var last_label = null;
    var input = $(name)[0];

    $(name).autocomplete({
        source: function(data, callback){
            if (last_label != data.term) {
                $(name + "_id").val("");
            }

            var json_url = '';
            if (typeof(url) == "function") {
                json_url = url();
            } else {
                json_url = url;
            }

            $.getJSON(
                json_url, {
                    term: $(name).val(),
                },
                callback
            );
        },
        open: function () {
            if (last_label != input.value) {
                $(name + "_id").val("");
            }
        },
        select: function (event, ui) {
            if (ui.item.label) {
                if (ui.item.id) {
                    $(name + "_id").val(ui.item.id);
                } else {
                    $(name + "_id").val(ui.item.value);
                }
                $(name).val(ui.item.label);
                last_label = ui.item.label;
                last_id = ui.item.value;
            } else {
                $(name + "_id").val(ui.item.id);
                last_label = ui.item.value;
                last_id = ui.item.id;
            }

            $(name + "_id")[0].selected_data = ui.item;
            $(name + "_id").change();
            return false;
        },
    });

    $(input).on('blur', function(){
        if (input.value == '') {
            $(name + "_id").val("");
        }
        if ($(name + "_id").val() == "") {
            input.value = "";
        }
    });
}

function save_stock()
{
    var $modal = $("#stock-modal");
    var stock = {};
    stock.id = $modal.find("[name=id]").val();
    stock.purchasing_item_id = $modal.find("[name=purchasing_item_id]").val();
    stock.supplier_id = $modal.find("[name=supplier_id]").val();
    stock.item_id = $modal.find("[name=item_id]").val();
    stock.amount_type = $modal.find("[name=amount_type]").val();
    stock.amount = $modal.find("[name=amount]").val();
    stock.expiry_date = $modal.find("[name=expiry_date]").val();
    stock.location_id = $modal.find("[name=location_id]").val();

    $.post(
        "/admin/inventory/stock_save",
        stock,
        function (response) {
            $("#stock-modal").modal('hide');
            list_stocks();
        }
    )
}

function save_checkout()
{
    var $modal = $("#checkout-modal");
    var checkout = {};
    checkout.id = $modal.find("[name=id]").val();
    checkout.requestee_id = $modal.find("[name=requestee_id]").val();
    checkout.stock_id = $modal.find("[name=stock_id]").val();
    checkout.amount_type = $modal.find("[name=amount_type]").val();
    checkout.amount = $modal.find("[name=amount]").val();
    checkout.location_id = $modal.find("[name=location_id]").val();

    $.post(
        "/admin/inventory/checkout_save",
        checkout,
        function (response) {
            $("#checkout-modal").modal('hide');
            list_stocks();
        }
    )
}

function save_checkin()
{
    var $modal = $("#checkin-modal");
    var checkin = {};
    checkin.id = $modal.find("[name=id]").val();
    checkin.requestee_id = $modal.find("[name=requestee_id]").val();
    checkin.checkout_id = $modal.find("[name=checkout_id]").val();
    checkin.amount_type = $modal.find("[name=amount_type]").val();
    checkin.amount = $modal.find("[name=amount]").val();
    checkin.location_id = $modal.find("[name=location_id]").val();

    $.post(
        "/admin/inventory/checkin_save",
        checkin,
        function (response) {
            $("#checkin-modal").modal('hide');
            list_stocks();
        }
    )
}

$(document).on("ready", function(){
    list_stocks();

    var date_range_change_timeout = null;
    $("#stock-period").on("change", function(){
        if (date_range_change_timeout) {
            clearTimeout(date_range_change_timeout);
        }
        date_range_change_timeout = setTimeout(list_stocks, 30);
    });

    $("#new-stock").on("click", function(){
        edit_stock();
    });

    $("#stock-modal").find(".btn.save").on("click", function() {
        if ($('#add-stock-form').validationEngine('validate')) {
            save_stock();
        }
    });

    autocomplete_set('#stock-purchasing_item', '/admin/inventory/purchasing_autocomplete');
    autocomplete_set('#stock-supplier', '/admin/contacts3/autocomplete_contacts?subtype=Suppliers');
    autocomplete_set(
        '#stock-item',
        function(){
            var url = '/admin/inventory/items_autocomplete?purchase_item_id=' + $("#stock-purchasing_item_id").val();
            return url;
        }
    );
    autocomplete_set('#stock-location', '/admin/courses/autocomplete_locations');
    autocomplete_set('#checkout-location', '/admin/courses/autocomplete_locations');
    autocomplete_set('#checkout-requestee', '/admin/contacts3/autocomplete_contacts');
    autocomplete_set('#checkin-location', '/admin/courses/autocomplete_locations');
    autocomplete_set('#checkin-requestee', '/admin/contacts3/autocomplete_contacts');

    $("#stock-purchasing_item_id").on("change", function(){
        $("#stock-amount_type").val($("#stock-purchasing_item_id")[0].selected_data.amount_type);
        $(".po-request-product-amount-icon").html(amount_types[$("#stock-purchasing_item_id")[0].selected_data.amount_type]);
        $(".po-request-product-amount-icon").attr("title", amount_labels[$("#stock-purchasing_item_id")[0].selected_data.amount_type]);
        $("#stock-supplier").val($("#stock-purchasing_item_id")[0].selected_data.supplier);
        $("#stock-supplier_id").val($("#stock-purchasing_item_id")[0].selected_data.supplier_id);
    });

    $("#stock-item_id").on("change", function(){
        $("#stock-amount_type").val($("#stock-item_id")[0].selected_data.amount_type);
        $(".po-request-product-amount-icon").html(amount_types[$("#stock-item_id")[0].selected_data.amount_type]);
        $(".po-request-product-amount-icon").attr("title", amount_labels[$("#stock-item_id")[0].selected_data.amount_type]);
    });

    $("#stock-amount_type").on("change", function(){
        $(".po-request-product-amount-icon").html(amount_types[this.value]);
        $(".po-request-product-amount-icon").attr("title", amount_labels[this.value]);
    });

    $("#checkin-amount_type").on("change", function(){
        $(".po-request-product-amount-icon").html(amount_types[this.value]);
        $(".po-request-product-amount-icon").attr("title", amount_labels[this.value]);
        $(".amount_label").html(amount_labels[this.value]);
    });

    $("#checkout-amount_type").on("change", function(){
        $(".po-request-product-amount-icon").html(amount_types[this.value]);
        $(".po-request-product-amount-icon").attr("title", amount_labels[this.value]);
        $(".amount_label").html(amount_labels[this.value]);
    });

    $("#stock-table tbody").on("click", ".view", function(){
        var id = $(this).data("id");
        $.post(
            "/admin/inventory/stock_details",
            {id: id},
            function (stock) {
                edit_stock(stock);
            }
        )
    });

    $("#stock-table tbody").on("click", ".checkout", function(){
        var id = $(this).data("id");
        $.post(
            "/admin/inventory/stock_details",
            {id: id},
            function (stock) {
                edit_checkout(stock);
            }
        )
    });

    $("#stock-table tbody").on("click", ".view-checkout", function(){
        var id = $(this).data("checkout_id");
        $.post(
            "/admin/inventory/checkout_details",
            {id: id},
            function (checkout) {
                edit_checkout(null, checkout);
            }
        )
    });

    $("#stock-table tbody").on("click", ".view-checkin", function(){
        var id = $(this).data("checkin_id");
        $.post(
            "/admin/inventory/checkin_details",
            {id: id},
            function (checkin) {
                edit_checkin(null, checkin);
            }
        )
    });

    $("#stock-table tbody").on("click", ".checkin", function(){
        var checkout_id = $(this).data("checkout_id");
        $.post(
            "/admin/inventory/checkout_details",
            {id: checkout_id},
            function (checkout) {
                edit_checkin(checkout);
            }
        );
    });

    $("#check-in").on("click", function(){
        edit_checkin();
    });

    $("#checkin-modal").find(".btn.save").on("click", function() {
        if ($('#checkin-form').validationEngine('validate')) {
            save_checkin();
        }
    });

    $("#check-out").on("click", function(){
        edit_checkout();
    });

    $("#checkout-modal").find(".btn.save").on("click", function() {
        if ($('#checkout-form').validationEngine('validate')) {
            save_checkout();
        }
    });

    $(".stock-list-filter").on("change", function(){
       list_stocks();
    });
});

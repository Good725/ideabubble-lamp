function edit_item(item)
{
    var $modal = $("#inventory-item-modal");
    var $form = $("#inventory-edit-form");

    $form.find("input[type=text]").val("");
    $form.find("input[type=radio]").prop("checked", false);
    $form.find("select").prop("selectedIndex", -1);
    $form.find("[name=use][value=\"Single\"]").prop("checked", true).click();

    if (item) {
        $form.find("[name=id]").val(item.id);
        $form.find("[name=title]").val(item.title);
        $form.find("[name=category_id]").val(item.category_id);
        $form.find("[name=category_id]").change();
        $form.find("[name=use][value=" + item.use + "]").prop("checked", true).click();
        $form.find("[name=amount_type]").val(item.amount_type).change();

        $form.find(".vat_rate").toggleClass('hidden', !item.vat_rate);
        $form.find("[name=vat_rate_enable]").prop('checked', !!item.vat_rate);

        if (item.vat_rate) {
            $form.find("[name=vat_rate]").val(item.vat_rate);
        }
    }

    $modal.find('.inventory-edit-modal-add_only').toggleClass('hidden', item);
    $modal.find('.inventory-edit-modal-edit_only').toggleClass('hidden', !item);

    $modal.modal();
}

function save_item()
{
    var item = {};
    item.id = $("#inventory-edit-form [name=id]").val();
    item.title = $("#inventory-edit-form [name=title]").val();
    item.category_id = $("#inventory-edit-form [name=category_id]").val();
    item.use = $("#inventory-edit-form [name=use]:checked").val();
    item.amount_type = $("#inventory-edit-form [name=amount_type]").val();
    item.vat_rate = $("#inventory-edit-form [name=vat_rate_enable]").prop("checked") ?
        $("#inventory-edit-form [name=vat_rate]").val()
        :
        null;

    $.post(
        "/admin/inventory/item_save",
        item,
        function (response) {
            $("#inventory-item-modal").modal('hide');
            list_items();
        }
    )
}

function list_items()
{
    var $table = $("#inventory-table");

    $table.ib_serverSideTable(
        '/admin/inventory/items',
        {
            aaSorting: [[ 5, 'desc']],
            sServerMethod: "POST",
            fnServerParams: function (params) {

            }
        },
        {
            responsive: true,
            //row_data_ids: true,
            draw_callback: function() {

            }
        }
    );
}

$('#inventory-table').on('click', '.publish_toggle', function() {
    var id = $(this).data('id');
    var new_status = $(this).data('publish') ? 0 : 1;
    $.ajax('/admin/inventory/ajax_toggle_publish/'+id+'?publish='+new_status).done(function(data) {
        if (data.success) {
            list_items();
        }

        $('#inventory-alert_area').add_alert(data.message, (data.success ? 'success' : 'error')+' popup_box');
    });
});

$(document).on("ready", function(){
    $(".btn.item-add").on("click", function(){
        edit_item();
    });

    $("#inventory-item-modal button.save").on("click", function(){
        save_item();
    });

    $("#inventory-table tbody").on ("click", "button.view", function(){
        var id = $(this).data("id");
        $.post(
            "/admin/inventory/item_view",
            {id: id},
            function response(item) {
                edit_item(item);
            }
        )
    });

    $("[name=vat_rate_enable]").on("change", function(){
        if (this.checked) {
            $(".vat_rate").removeClass("hidden");
        } else {
            $(".vat_rate").addClass("hidden");
        }
    });

    $("#inventory-amount_type").on("change", function(){
        var types = {
            "Volume" : "Lt",
            "Weight" : "Kg",
            "Unit" : "qty"
        };
        $("#inventory-amount_type_detail").val(types[this.value] || "kg");
    });

    list_items();
});

function clear_product_edit()
{
    $("#edit-product-form input, #edit-product-form select, #edit-product-form textarea").val("");
    $("#edit-product-form [name=deleted]").val("0");
    $("#delete-product-button").addClass("hidden");
    $("#edit-product-form [name=id]").prop("readonly", false);
    $("#edit-product-form [name=name]").prop("readonly", false);
    $("#edit-product-form [name=value]").prop("readonly", false);
    $("#edit-product-form button").prop("disabled", false);
}

$(document).ready(function()
{
	var $table = $('#list-donations-table');
	$("#edit-product-form").on("submit", function(e){
        e.preventDefault();
        var form = this;
        var data = $(this).serialize();
        $("#edit-product-form button").prop("disabled", true);
        $.post(
            $(this).attr("action"),
            data,
            function (response) {
				window.location.reload();
            }
        )
        return false;
    });

});

$(document).on("click", "a.product-edit", function(){
    clear_product_edit();

    var product_id = $(this).data("id");
    if (product_id != 'new') {
        $.post(
            "/admin/donations/product",
            {
                id: product_id
            },
            function (response) {
                if (response.product) {
                    $("#edit-product-form [name=id]").val(response.product.id);
                    $("#edit-product-form [name=name]").val(response.product.name);
                    $("#edit-product-form [name=value]").val(response.product.value);
                    $("#edit-product-form [name=status]").val(response.product.status);
                    if (!response.product.requested) {
                        $("#delete-product-button").removeClass("hidden");
                    } else {
                        $("#edit-product-form [name=id]").prop("readonly", true);
                        $("#edit-product-form [name=name]").prop("readonly", true);
                        $("#edit-product-form [name=value]").prop("readonly", true);
                    }
                    $("#edit-product-modal").modal("show");
                }
            }
        );
    } else {
        $("#edit-product-modal").modal("show");
    }

    $("#save-product-button").on("click", function(){
        $("#edit-product-form [name=action]").val("save");
    });

    $("#delete-product-button").on("click", function(){
        $("#edit-product-form [name=deleted]").val("1");
        $("#edit-product-form [name=action]").val("delete");
    })
});

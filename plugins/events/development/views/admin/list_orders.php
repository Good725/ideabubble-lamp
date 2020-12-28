<?=(isset($alert)) ? $alert : ''?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<?php
$currencies = Model_Currency::getCurrencies(true);
?>

<table class="table table-striped dataTable table-condensed " id="list-orders-table">
    <thead>
        <tr>
            <th scope="col"><?= __('ID#') ?></th>
            <th scope="col">Buyer</th>
            <th scope="col">Email</th>
            <th scope="col">Item</th>
            <th scope="col">Status</th>
            <th scope="col">Message</th>
            <th scope="col">Order Date</th>
            <th scope="col">Amount</th>
            <th scope="col">Actions</th>
        </tr>
    </thead>
</table>

<div class="modal fade" tabindex="-1" role="dialog" id="email-details-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= __('Message Details') ?></h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Close') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="email-order-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/events/ticket/" method="post">
                <input type="hidden" name="id" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?= __('Email Ticket') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label"><?=__('Recipient')?></label>
                        <input class="form-control" type="text" name="recipient" value="" placeholder="Leave empty to send to default email of the buyer" />
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?=__('Message')?></label>
                        <textarea class="form-control" name="message" placeholder="Leave empty to use default message"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger send" data-dismiss="modal" id="email-order-button"><?= __('Send') ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function()
    {
        var $table = $("#list-orders-table");

        $table.ready(function() {
            var ajax_source = '/admin/events/ajax_get_orders_datatable';
            var settings = {
                "bAutoWidth"      : false,
                "bDestroy"        : false,
                "bProcessing"     : false,
                "bServerSide"     : true,
                "fnDrawCallback"  : function(oSettings) {
                    $table.find('thead th').css('width', '');
                },
                "oLanguage"       : { "sInfoFiltered": '' },
                "sDom"            : 'lfrtip',
                "sPaginationType" : 'bootstrap',
                "aaSorting"       : []
            };
            var drawback_settings = {"fnDrawCallback"  : function(oSettings) {
                    $table.find('thead th').css('width', '');
                }};
            // Serverside table
            $table.ib_serverSideTable(ajax_source, settings, drawback_settings);
        });

        $table.on("click", ".list-email-button", function(){
            $("#email-order-modal [name=id]").val($(this).data("id"));
            $("#email-order-modal form").attr("action", "/admin/events/ticket?order_id=" + $(this).data("id") + "&ticket_id=&action=email");

        });

        $("#email-order-modal form button.send").on("click",function(){
            var url = this.form.action;
            var data = {};
            data.message = this.form.message.value;
            data.recipient = this.form.recipient.value;
            $.post(
                url,
                data,
                function (response) {
                    if (response.result > 0) {
                        alert('Ticket has been emailed.');
                    } else {
                        alert("Unable to send email");
                    }
                }
            );
        });

        $table.on("click", ".list-archive-button", function(){
            var orderId = $(this).data("id");
            var url = "/admin/events/order_archive";
            $.post(
                url,
                {order_id: orderId, archive: 1},
                function (response) {
                    if (response.result) {
                        window.location.reload();
                    } else {
                        alert("Unable to archive");
                    }
                }
            );
            return false;
        });

        $table.on("click", ".list-unarchive-button", function(){
            var orderId = $(this).data("id");
            var url = "/admin/events/order_archive";
            $.post(
                url,
                {order_id: orderId, archive: 0},
                function (response) {
                    if (response.result) {
                        window.location.reload();
                    } else {
                        alert("Unable to unarchive");
                    }
                }
            );
            return false;
        });

        $table.on("click", ".order_message_details", function(){
            var message_id = $(this).data('message_id');
            $("#email-details-modal .modal-body")
                .load(
                    "/admin/messaging/details?message_id=" + message_id + " #messaging_details",
                    {},
                    function(response) {
                        $("#email-details-modal").modal();
                    }
                );
            return false;
        });

        $table.on("click", ".order_message_send", function(){
            var order_id = $(this).data('order_id');
            $.post(
                '/frontend/events/email_order',
                {order_id: order_id},
                 function(response) {

                 }
            );
            return false;
        });
    });
</script>

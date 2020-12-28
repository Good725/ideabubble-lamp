<?php require_once Kohana::find_file('template_views', 'header') ?>

<?php
if ($order['error']) {
?>
    <p><?=$order['error']?></p>
<?php
} else {
?>
<div>
<form id="checkout" method="post">
    <input type="hidden" name="event_id" value="<?=$order['event_id']?>" />
    <input type="hidden" name="total" value="<?=$order['total']?>" />
    <?php foreach ($order['items'] as $itemIndex => $item) { ?>
        <input type="hidden" name="item[<?=$itemIndex?>][ticket_type_id]" value="<?=$item['ticket_type_id']?>" />
        <input type="hidden" name="item[<?=$itemIndex?>][quantity]" value="<?=$item['quantity']?>" />
        <?php foreach ($item['dates'] as $dateId) {
        ?>
            <input type="hidden" name="item[<?=$itemIndex?>][dates][]" value="<?=$dateId?>" />
        <?php
        }
        ?>
    <?php } ?>
    <h1>Checkout</h1>

    <fieldset>
        <legend>Items</legend>
        <table class="table">
            <thead>
                <tr><th>Type</th><th>Ticket</th><th>Quantity</th><th>Price</th></tr>
            </thead>
            <tbody>
            <?php
            foreach ($order['items'] as $itemIndex => $item) {
                foreach ($event['ticket_types'] as $ticketType) {
                    if ($ticketType['id'] == $item['ticket_type_id']) {
                        ?>
                <tr>
                    <td>
                        <?=$ticketType['type']?>
                        <?php
                        if ($ticketType['type'] == 'Donation') {
                        ?>
                            <input type="text" class="item_donation" name="item[<?=$itemIndex?>][donation]" value="<?=$item['donation'] ? $item['donation'] : '1.00'?>" readonly="readonly" />
                        <?php
                        }
                        ?>
                    </td>
                    <td><?=$ticketType['name']?></td>
                    <td><?=$item['quantity']?></td>
                    <td><input type="text" class="item_total" name="item[<?=$itemIndex?>][total]" value="<?=$item['total']?>" readonly="readonly" /></td>
                </tr>
                        <?php
                    }
                }
            }
            ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" align="right">Total: </th>
                    <th><?=$order['currency'] . '<span id="total_display">' . number_format($order['total'], 2) . '</span>'?></th>
                </tr>
            </tfoot>
        </table>
    </fieldset>
    <fieldset class="col-sm-6">
        <legend>Billing</legend>
        <div class="form-group">
            <label class="col-sm-2">First Name</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" name="firstname" required="required" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2">Last Name</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" name="lastname" required="required" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2">Email</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" name="email" required="required" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2">Address 1</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" name="address_1" required="required" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2">Address 2</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" name="address_2" required="required" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2">City</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" name="city" required="required" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2">Post Code/Eir Code</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" name="postcode" required="required" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2">Country</label>
            <div class="col-sm-4">
                <select class="form-contrl" name="country_id" required="required" >
                    <?=html::optionsFromRows('id', 'name', Model_Event::getCountryMatrix())?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2">County</label>
            <div class="col-sm-4">
                <select class="form-contrl" name="county_id">

                </select>
            </div>
        </div>
    </fieldset>
    <fieldset class="col-sm-6">
        <legend>Payment</legend>
        <div class="form-group">
            <label class="col-sm-2">Credit Card Type</label>
            <div class="col-sm-10"><select class="form-control" name="ccType" required="required" ><option value=""></option> <option value="visa">VISA</option><option value="mc">Master Card</option> </select> </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2">Name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="ccName" maxlength="30" required="required" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2">Number</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="ccNum" maxlength="20" required="required" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2">CVC</label>
            <div class="col-sm-2">
                <input type="text" class="form-control" name="ccCVC" maxlength="4" required="required" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2">Expiration</label>
            <div class="col-sm-10">
                <select name="ccYear" required="required" >
                    <option value="">Year</option>
                <?php for($i = 0 ; $i < 20 ; ++$i) { ?>
                    <option value="<?=date('Y') + $i?>"><?=date('Y') + $i?></option>
                <?php } ?>
                </select>
                <select name="ccMonth" required="required" >
                    <option value="">Month</option>
                    <?php for($i = 1 ; $i <= 12 ; ++$i) { ?>
                        <option value="<?=str_pad($i, 2, '0', STR_PAD_LEFT)?>"><?=$i?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <button type="submit" name="action" value="buy">Buy</button>
    </fieldset>
</form>
</div>
<script>
function disableScreenShow()
{
    if(!window.disableScreenDiv){
        window.disableScreenDiv = document.createElement( "div" );
        window.disableScreenDiv.style.display = "block";
        window.disableScreenDiv.style.position = "fixed";
        window.disableScreenDiv.style.top = "0px";
        window.disableScreenDiv.style.left = "0px";
        window.disableScreenDiv.style.right = "0px";
        window.disableScreenDiv.style.bottom = "0px";
        window.disableScreenDiv.style.textAlign = "center";
        window.disableScreenDiv.style.zIndex = 99999999;
        window.disableScreenDiv.innerHTML = '<div style="position:absolute;top:0px;left:0px;right:0px;bottom:0px;background-color:#000000;opacity:0.2;filter:alpha(opacity=20);z-index:1;"></div>' +
            '<img src="/engine/shared/img/ajax-loader.gif" style="position:absolute;top:50%;left:50%;margin:-16px;z-index:2;" alt="processing..."/>';

        document.body.appendChild(window.disableScreenDiv);
    }
    window.disableScreenDiv.style.visibility = "visible";
}

function disableScreenHide()
{
    if (window.disableScreenDiv) {
        window.disableScreenDiv.style.visibility = "hidden";
    }
}
var countries = <?=json_encode(Model_Event::getCountryMatrix())?>;
$(document).on("ready", function(){
    $("#checkout [name=action][value=buy]").on("click", function(){
        this.innerHTML = 'Processing...';
        disableScreenShow();
        var data = $("form#checkout").serialize();
        $.post(
            '/frontend/events/process_order',
            data,
            function (response){
                if (response.error) {
                    disableScreenHide();
                    alert(response.error);
                } else {
                    window.location = '/mytickets.html';
                }
            }
        );
        return false;
    });
});
</script>
<?php
}
?>

<?php require_once Kohana::find_file('template_views', 'footer') ?>


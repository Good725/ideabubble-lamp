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
<div>
<form method="post">
    <h1>Invoice Details</h1>
    <p>Created: <?=$invoice['created']?><br />
        <br />
        Amount: <?=$invoice['currency'] . $invoice['amount']?><br />
        Event: <?=$invoice['event']['name']?>
    </p>

    <div class="form-group clearfix">
        <label class="col-sm-2">Due Date: </label>
        <div class="col-sm-2">
            <input type="text" class="form-control datepicker" name="due" value="<?=$invoice['due'] ? date::ymd_to_dmy($invoice['due']) : ''?>" />
        </div>
    </div>

    <div class="form-group clearfix">
        <label class="col-sm-2">Date Completed: </label>
        <div class="col-sm-2">
            <input type="text" class="form-control datepicker" name="completed" value="<?=$invoice['completed'] ? date::ymd_to_dmy($invoice['completed']) : ''?>" />
        </div>
    </div>


    <div class="well text-center">
        <button type="submit" name="action" value="save" class="btn btn-primary continue-button"><?= __('Save') ?></button>
        <a href="/admin/events/invoices" class="btn btn-default"><?= __('Cancel') ?></a>
    </div>
</form>
</div>

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
<!-- FORMATS -->

<div class="main-container">
    <h4 class="">Select Type Of Discount<font style="color:#ff0000">*</font> <span style="float:right;">Select type of user to check Report<font style="color:#ff0000">*</font></span></h4>
	<style>
		#form_add_edit_format [class^="col-sm"]{padding: 0 2px;}
	</style>

    <!-- FORM -->
    <form class="well form-horizontal clearfix" id="cart_discount">
        <div>
            <!-- FIELDS -->
			<div class="col-sm-6">
				<select class="form-control" id="discount_types">
					<option value="">-- Select Type --</option>
					<?php foreach ($discount_types as $item): ?>
						<option value="<?=$item['id']?>"><?=$item['title']?></option>
                	<?php endforeach; ?>
				</select>
			</div>

			<div class="col-sm-4">
				<select class="form-control" id="format_type">
					<option value="">-- Select Type --</option>
					<option value="Applied">Applied</option>
					<option value="Displayed">Displayed</option>
				</select>
			</div>

		</div>
    </form>
    <div id="report_result"></div>

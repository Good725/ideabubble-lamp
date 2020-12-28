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
    <h4 class="">Formats</h4>
	<style>
		#form_add_edit_format [class^="col-sm"]{padding: 0 2px;}
	</style>

    <!-- FORM -->
    <form class="well form-horizontal clearfix" id="form_add_edit_format">
        <div>
            <!-- FIELDS -->
			<div class="col-sm-3">
				<input type="text" class="form-control required" name="format_title" id="format_title" placeholder="Enter title here"/>
			</div>
			<div class="col-sm-3 ">
				<input type="text" class="form-control" id="format_description" placeholder="Enter description here"/>
			</div>
			<div class="col-sm-3">
				<select class="form-control" id="format_type">
					<option value="">-- Select Type --</option>
					<?php foreach ($types as $item): ?>
						<option value="<?=$item['id']?>"><?=$item['description']?></option>
                	<?php endforeach; ?>
				</select>
			</div>
			<div class="col-sm-1">
				<input type="text" class="form-control" id="code" placeholder="Code" />
			</div>
			<div class="col-sm-2">
				<select class="form-control" id="role" name="role[]" multiple=""  class="input-medium">
					<option value="none" selected>None</option>
					<? foreach ($roles as $role): ?>
						<option value="<?=$role['id']?>"><?=$role['role']?></option>
					<? endforeach; ?>
				</select>
			</div>

		</div>
        <?php if (Settings::instance()->get('product_discount_category_enabled') == 1) { ?>
        <div>
            <div class="col-sm-12" style="max-height: 200px; overflow: auto; border-style: solid; border-width: 1px;">
                <label class="col-sm-3">Categories</label>
                <ul class="col-sm-9">
                <?php
                foreach ($categories as $pcategory) {
                ?>
                    <li><label><?=$pcategory['category']?><input type="checkbox" name="has_category[]" value="<?=$pcategory['id']?>" /> </label></li>
                <?php
                }
                ?>
                </ul>
            </div>
        </div>
        <?php } ?>
		<div>
			<div class="col-sm-3">
				<input id="datetimepicker_from" class="form-control" type="text"  placeholder="Date of available from" />
			</div>
			<div class="col-sm-3">
				<input id="datetimepicker_to" class="col-sm-3 form-control" type="text"  placeholder="Date of available till" />
			</div>

            <!-- ACTIONS -->
            <span class="col-sm-2" id="add_format_actions">
                <button type="submit" class="btn btn-primary" id="save_format">Add Format</button>
            </span>
            <span class="col-sm-4" id="edit_format_actions" style="display: none;">
                <button type="submit" class="btn btn-primary" id="update_format">Save</button>
                <button type="reset" class="btn btn-danger" id="cancel_format_update">Cancel</button>
            </span>
        </div>
    </form>

    <!-- TABLE -->
    <div class="table-container">
        <table class="table table-striped dataTable" id="formats_table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Code</th>
                    <th>Users Roles</th>
                    <th>Available from</th>
                    <th>Available till</th>
                    <th>Edit</th>
                    <th>Publish</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- RATES -->
<div class="main-container">
    <h4 class="">Rates</h4>

    <!-- FORM -->
    <form class="pull-right well form-inline" id="form_add_edit_rate">
        <div class="form-group">
            <!-- FIELDS -->
            <select class="input-medium required" name="format_id" id="format_id"></select>
            <input type="text" class="input-small" id="range_from" placeholder="Range From"/>
            <input type="text" class="input-small" id="range_to" placeholder="Range To"/>
            <input type="text" class="input-small" id="discount_rate" placeholder="Discount Rate"/>
            <input type="text" class="input-small" id="discount_rate_percentage" placeholder="Discount Rate Percentage"/>

            <!-- ACTIONS -->
            <span id="add_rate_actions">
                <button type="submit" class="btn btn-primary" id="save_rate">Add Rate</button>
            </span>
            <span id="edit_rate_actions" style="display: none;">
                <button type="submit" class="btn btn-primary" id="update_rate">Save</button>
                <button type="reset" class="btn btn-danger" id="cancel_rate_update">Cancel</button>
            </span>
        </div>
    </form>

    <!-- TABLE -->
    <div class="table-container">
        <table class="table table-striped dataTable" id="rates_table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Range From</th>
                    <th>Range To</th>
                    <th>Discount</th>
                    <th>Cart Discount</th>
                    <th>Edit</th>
                    <th>Publish</th>
                    <th>Delete</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal fade" id="confirm_delete">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h3>Warning!</h3>
			</div>
			<div class="modal-body" id="warning_message"><!-- DO NOT ENTER TEXT HERE --></div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
				<a href="#" class="btn btn-danger" id="btn_delete">Delete</a>
			</div>
		</div>
	</div>
</div>

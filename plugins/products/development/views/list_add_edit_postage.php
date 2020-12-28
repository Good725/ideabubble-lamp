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

    <!-- FORM -->
    <form class="pull-right well form-inline" id="form_add_edit_format">
        <div class="form-group">
            <!-- FIELDS -->
            <input type="text" class="form-control required" name="format_title" id="format_title" placeholder="Enter format title here"/>

            <!-- ACTIONS -->
            <span id="add_format_actions">
                <button type="submit" class="btn btn-primary" id="save_format">Add Format</button>
            </span>
            <span id="edit_format_actions" style="display: none;">
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
                    <th>Edit</th>
                    <th>Publish</th>
                    <th>Delete</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- ZONES -->
<div class="main-container">
    <h4 class="">Zones</h4>

    <!-- FORM -->
    <form class="pull-right well form-inline" id="form_add_edit_zone">
        <div class="form-group">
            <!-- FIELDS -->
            <input type="text" class="form-control required" name="zone_title" id="zone_title" placeholder="Enter zone title here"/>

            <!-- ACTIONS -->
            <span id="add_zone_actions">
                <button type="submit" class="btn btn-primary" id="save_zone">Add Zone</button>
            </span>
            <span id="edit_zone_actions" style="display: none;">
                <button type="submit" class="btn btn-primary" id="update_zone">Save</button>
                <button type="reset" class="btn btn-danger" id="cancel_zone_update">Cancel</button>
            </span>
        </div>
    </form>

    <!-- TABLE -->
    <div class="table-container">
        <table class="table table-striped dataTable" id="zones_table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Edit</th>
                    <th>Publish</th>
                    <th>Delete</th>
                </tr>
            </thead>
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

			<select class="form-control required" name="country_id" id="country_id"></select>
			<select class="form-control required" name="format_id" id="format_id"></select>
            <select class="form-control required" name="zone_id" id="zone_id"></select>
            <input type="text" class="form-control edit-postage-weight-input" id="weight_from" placeholder="Weight From" />
            <input type="text" class="form-control edit-postage-weight-input" id="weight_to" placeholder="Weight To"/>
            <input type="text" class="form-control" id="price" placeholder="Price"/>

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
                    <th scope="col">ID</th>
					<th scope="col">Country</th>
					<th scope="col">Format</th>
                    <th scope="col">Zone</th>
                    <th scope="col">Start Weight</th>
                    <th scope="col">End Weight</th>
                    <th scope="col">Price</th>
                    <th scope="col">Edit</th>
                    <th scope="col">Publish</th>
                    <th scope="col">Delete</th>
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

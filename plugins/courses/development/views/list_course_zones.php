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
<div id="message"></div>
<table class="table table-striped dataTable" id="zones_table">
	<thead>
		<tr>
			<th>Name</th>
<!--			<th>Price</th>-->
			<th>Edit</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>

<!-- zone edit topic -->
<div class="modal fade booking-discount-modal" tabindex="-1" role="dialog" id="edit-zone-popup-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="custom-form-horizontal">
                <div class="modal-header booking-discount-modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Edit Zone</h4>
                </div>
                <div class="modal-body clearfix">
                    <input id="edit_zone_popup_id" type="hidden" name="id">
                    <div class="form-group">
                        <label for="edit_zone_popup_name" class="control-label">Zone’s Name</label>
                        <input id="edit_zone_popup_name" class="form-control" name="from" value="" type="text">
                    </div>
                </div>
                <div class="modal-footer form-action-group">
                    <button id="edit_zone_popup_save" type="button" class="save_btn btn  btn-primary">Save</button>
                    <button type="button" class="btn-link" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- zone delete-->
<div class="modal fade" id="confirm_delete">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3>Warning!</h3>
            </div>
            <div class="modal-body">
                <p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected Zone.</p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal">Cancel</a>
                <a href="#" class="btn btn-danger" id="btn_delete_yes">Delete</a>
            </div>
        </div>
    </div>
</div>
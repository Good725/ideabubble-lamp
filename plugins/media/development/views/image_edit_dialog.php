<div id="image_editor_wrapper">

	<div id="edit_image_modal" class="modal fade">
		<div class="modal-dialog" style="width: 95%;max-width: 1050px">
			<div class="modal-content">
				<div class="hidden" id="edit_image_source"></div>

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">×</button>
					<h3>Edit Image</h3>
				</div>

				<div class="modal-body clearfix">
					<!-- Content -->
					<iframe id="image-edit-frame" marginheight="0" marginwidth="0" src="about:blank" width="100%" height="690" frameborder="0" style="max-height: -webkit-calc(100vh - 190px);max-height: calc(100vh - 190px);"></iframe>
				</div>

				<div class="modal-footer">
					<a class="btn save_btn" id="image-edit-save">Save</a>
					<a class="btn" data-dismiss="modal" id="image-edit-close">Cancel</a>
				</div>

			</div>
		</div>
	</div>
</div>
<div id="upload_document_modal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3>Upload Documents</h3>
			</div>
			<div class="modal-body form-horizontal">
				<div class="alert-area"></div>
	
			
						<label class="control-label text-left">Choose a document and upload</label>
		
							<form method="POST" enctype="multipart/form-data" action="" id="upload_document_form" class="form-search">
								<input type="file" id="upload_document_modal_file" name="file" class="file_input" />
								<input type="hidden" value="document" name="doc_type">
								<input type="hidden" id="upload_document_modal_contact_id" name="contact_id" >
								<br>
								<label for="upload_document_modal_import_to_directory">Directory:</label>
	<!--							<select name="import_to_directory" id="upload_document_modal_import_to_directory" class="input-medium">-->
	<!--								--><?php
	//								$docs = new Model_Document();
	//								$save_to_options = $docs->get_folder_list();
	//								foreach ($save_to_options as $key => $directory)
	//								{
	//									echo '<option value="' . $directory['folder_name'] . '">' . $directory['friendly_name'] . '</option>';
	//								}
	//								?>
	<!--							</select>-->
							</form>
							
		
			
			</div>
			<div class="modal-footer">
				<a href="#" id="upload_document_modal_btn" class="btn btn-primary">Upload</a>
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
			</div>
		</div>
	</div>
</div>

<?php $data = (count($_POST) > 0) ? $_POST : (isset($data) ? $data : array()) ?>

<div class="col-sm-12 page-header clearfix">
    <?= isset($alert) ? $alert : ''?>
    <?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
	?>
    <h2><?= (isset($data['file_id']) AND is_numeric($data['file_id'])) ? 'Edit File' : 'New File'?></h2>
    <div id="pathBreadcrumbs"></div>
</div>

<div class="col-sm-12">
	<div class="col-sm-6">
		<form class="form-horizontal" id="frmAddEditFile" action="/admin/files/save/" method="post" enctype="multipart/form-data">
			<!-- Name -->
			<div class="form-group">
				<label class="col-sm-2 control-label" for="fileName">Name</label>
				<div class="col-sm-10">
					<input type="text" class="form-control required" id="fileName" name="file_name" placeholder="File Name..." value="<?php echo isset($data['file_name']) ? $data['file_name'] : ''?>"/>
				</div>
			</div>

			<!-- Version -->
			<div class="form-group">
				<label class="col-sm-2 control-label" for="txtVersionFile">Version</label>
				<div class="col-sm-7">
					<input type="text" class="form-control <?php echo (isset($data['file_id']) AND is_numeric($data['file_id'])) ? '' : 'required'?>" id="txtVersionFile" readonly="readonly"/>
				</div>
				<div class="col-sm-3">
					<span class="btn btn-default" id="btnAddVersion"><input type="file" id="versionFile" name="version_file"/><span>Select File...</span></span>
				</div>
			</div>

			<input type="hidden" id="fileId" name="file_id" value="<?php echo isset($data['file_id']) ? $data['file_id'] : ''?>"/>
			<input type="hidden" id="parentId" name="parent_id" value="<?php echo isset($data['parent_id']) ? $data['parent_id'] : ''?>"/>

			<div class="form-actions">
				<button type="submit" class="btn btn-primary">Save</button>
				<button type="reset" class="btn">Reset</button>
			</div>
		</form>
	</div>

	<?php if (isset($data['file_id']) AND is_numeric($data['file_id'])): ?>
		<div class="col-sm-6">
			<table class="table table-striped" id="versionsTable">
				<thead>
				<tr>
					<th>File</th>
					<th>Size (KiB)</th>
					<th>Active</th>
					<th>Actions</th>
				</tr>
				</thead>
			</table>
		</div>
	<?php endif ?>
</div>

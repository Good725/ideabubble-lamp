<? $field_data = (count($_POST) > 0) ? $_POST : (isset($field_data) ? $field_data : array()) ?>
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
<form class="form-inline" id="form_list_gallery" name="form_list_gallery" action="/admin/gallery/list/" method="post">
    <label class="control-label" for="category">Category:</label>
    <select class="input-medium" id="category" name="category">
        <option value='all' <?=( ! isset($field_data['category']) OR ($field_data['category'] == 'all') ) ? 'selected="selected"' : '' ?>>Show all</option>
        <? foreach ($category_list as $item): ?>
        <option value="<?=$item?>" <?=( isset($field_data['category']) AND ($field_data['category'] == $item) ) ? 'selected="selected"' : '' ?>><?=$item?></option>
        <? endforeach; ?>
    </select>
</form>

<table class="table table-striped">
    <thead>
    <tr>
        <th>Image</th>
        <th>Title</th>
        <th>Category</th>
        <th>Order</th>
        <th>Edit</th>
        <th>Publish</th>
        <th>Delete</th>
    </tr>
    </thead>
    <tbody>
    <? foreach ($galleries as $g): ?>
    <tr>
        <td onclick="location.href='<?=URL::Site('admin/gallery/edit/'.$g['id'])?>'">
            <img src="<?=Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$g['photo_name'], 'gallery'.DIRECTORY_SEPARATOR.'_thumbs_cms')?>" alt="<?=$g['photo_name']?>">
        </td>
        <td onclick="location.href='<?=URL::Site('admin/gallery/edit/'.$g['id'])?>'"><?=$g['title']?></td>
        <td onclick="location.href='<?=URL::Site('admin/gallery/edit/'.$g['id'])?>'"><?=$g['category']?></td>
        <td onclick="location.href='<?=URL::Site('admin/gallery/edit/'.$g['id'])?>'"><?=$g['order']?></td>
        <td onclick="location.href='<?=URL::Site('admin/gallery/edit/'.$g['id'])?>'"><i class="icon-pencil"></i></td>
        <td class="publish" data-id="<?=$g['id']?>"><?=( ($g['publish'] == 1) ? '<i class="icon-ok"></i>' : '<i class="icon-remove"></i>' )?></td>
        <td class="delete" data-id="<?=$g['id']?>"><i class="icon-remove-circle"></i></td>
    </tr>
        <? endforeach; ?>
    </tbody>
</table>

<div class="modal fade" id="confirm_delete">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h3>Warning!</h3>
			</div>
			<div class="modal-body">
				<p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected gallery.</p>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
				<a href="#" class="btn btn-danger" id="btn_delete_yes">Delete</a>
			</div>
		</div>
	</div>
</div>

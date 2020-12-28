<?php
if (isset($alert))
{
    echo $alert;
}
?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<table class='table table-striped dataTable'>
	<thead>
		<tr>
		<th>Image (thumb)</th>
		<th>Title</th>
		<th>Position</th>
		<th>Order</th>
		<th>Image Name</th>
		<th>Last Modified</th>
		<th>Modified By</th>
		<th>Options</th>
        <th>Delete</th>
        <th>Publish</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($panels as $id => $panel): ?>
		<tr>
		<td>
			<a href='<?php echo URL::Site('admin/panels/add_edit_item/' . $panel['id']); ?>'>
                <?php
                if ((is_numeric($panel['image']) AND $panel['image'] != '0') OR $panel['image'] == 'new')
                {
                    $panel['image'] = DB::select(array('image', 'image'))->from('plugin_custom_scroller_sequence_items')
                        ->where('sequence_id', '=', $panel['image'])->and_where('deleted', '=', '0')->execute()->get('image');
                    echo '<strong>Custom sequence</strong>';
                }
                ?>
                <?php if ($panel['image'] != '' AND $panel['image'] != 'new' AND $panel['image'] != '0'): ?>
				    <img src="<?php echo Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$panel['image'], 'panels/_thumbs_cms');?>" width="120" alt="<?php echo $panel['image'];?>"/>
                <?php endif; ?>
			</a>
		</td>
		<td onclick="location.href='<?php echo URL::Site('admin/panels/add_edit_item/' . $panel['id']); ?>'"><?php echo $panel['title']; ?></td>
        <td onclick="location.href='<?php echo URL::Site('admin/panels/add_edit_item/' . $panel['id']); ?>'"><?php echo $panel['position']; ?></td>
        <td onclick="location.href='<?php echo URL::Site('admin/panels/add_edit_item/' . $panel['id']); ?>'"><?php echo $panel['order_no']; ?></td>
        <td onclick="location.href='<?php echo URL::Site('admin/panels/add_edit_item/' . $panel['id']); ?>'"><?php echo $panel['image']; ?></td>
        <td onclick="location.href='<?php echo URL::Site('admin/panels/add_edit_item/' . $panel['id']); ?>'"><?php echo $panel['date_modified']; ?></td>
		<td>
			<a href='<?php echo URL::Site('admin/panels/add_edit_item/' . $panel['id']); ?>'>
				<?php echo $panel['modified_by_role'].' '.$panel['modified_by_name']; ?>
			</a>
		</td>
        <td><a href='<?php echo URL::Site('admin/panels/add_edit_item/' . $panel['id']); ?>'>Edit</a></td>
        <td id="delete_<?=$panel['id']?>" class="delete"><i class="icon-remove-circle"></i></td>
		<td id="publish_<?=$panel['id']?>" class="publish" data-item_publish="<?php echo $panel['publish'];?>">
			<?php echo (($panel['publish'] == '1')? '<i class="icon-ok"></i>' : '<i class="icon-remove"></i>')?>
		</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

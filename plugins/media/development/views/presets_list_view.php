<table class='table table-striped dataTable'>
	<thead>
		<tr>
			<th>Preset Title</th>
			<th>Location (directory)</th>
			<th>Width</th>
			<th>Height</th>
			<th>Action</th>
			<th>Thumb</th>
			<th>Width (thumb)</th>
			<th>Height (thumb)</th>
			<th>Action (thumb)</th>
			<th>Last Modified</th>
			<th>Modified By</th>
			<th>Publish</th>
			<th>Delete</th>
		</tr>
	</thead>
	<tbody>
<?	foreach($media_presets_items as $media_preset):?>
		<tr preset-item-id="<?=$media_preset['id']?>">
			<td><?=$media_preset['title']?></td>
			<td><?=(!empty($media_preset['directory']))? $media_preset['directory'] : 'content'?></td>
			<td><?=$media_preset['height_large']?></td>
			<td><?=$media_preset['width_large']?></td>
			<td><?=$media_preset['action_large']?></td>
			<td><?=(($media_preset['thumb'] == '1')? 'Yes <i class="icon-ok"></i>' : 'No <i class="icon-remove"></i>')?></td>
			<td><?=$media_preset['height_thumb']?></td>
			<td><?=$media_preset['width_thumb']?></td>
			<td><?=$media_preset['action_thumb']?></td>
			<td><?=$media_preset['date_modified']?></td>
			<td><?=$media_preset['title']?></td>
			<td id="publish_<?=$media_preset['id']?>" class="publish" data-item_publish="<?php echo $media_preset['publish'];?>">
				<?php echo (($media_preset['publish'] == '1')? '<i class="icon-ok"></i>' : '<i class="icon-remove"></i>')?>
			</td>
			<td id="delete_<?=$media_preset['id']?>" class="delete"><i class="icon-remove-circle"></i></td>
		</tr>
<?	endforeach?>
	</tbody>
</table>
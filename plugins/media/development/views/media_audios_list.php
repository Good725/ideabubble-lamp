<table class='table table-striped dataTable'>
	<thead>
		<tr>
			<th>Type</th>
			<th>Filename</th>
			<th>Size (Kbs)</th>
			<th>Last Modified</th>
			<th>Modified By</th>
			<th>Download</th>
			<th>Delete</th>
		</tr>
	</thead>
	<tbody>
<?	foreach($media_audios as $media_audio):
        $media_item_path = Model_Media::get_path_to_media_item_admin(
            Kohana::$config->load('config')->project_media_folder,
            $media_audio['filename'],
            $media_audio['location']
        );
?>
		<tr image-item-id="<?=$media_audio['id']?>">
			<td>
				<span class="icon-file">&nbsp;</span>
			</td>
			<td><?=$media_audio['filename']?></td>
			<td><?=$media_audio['size']?></td>
			<td><?=$media_audio['date_modified']?></td>
			<td>modified by</td>
			<td>
				<?php
				if (@$selectionDialog) {
					?>
					<a tabindex="0"
					   data-url="<?=$media_item_path?>"
					   data-id="<?=$media_audio['id']?>"
					   data-filename="<?=$media_audio['filename']?>"><span class="icon-download"></span>&nbsp;select</a>
					<?php
				}
				?>
				<a href="<?php echo Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$media_audio['filename'], 'audios'); ?>" target="_blank">
					<span class="icon-download">&nbsp;</span>
				</a>
			</td>
			<td id="delete_<?=$media_audio['id']?>" class="delete_docs" onclick="delete_media_item(this);"><i class="icon-remove-circle"></i></td>
		</tr>
<?	endforeach?>
	</tbody>
</table>
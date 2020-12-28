<div class="row">
    <div class="span12">
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
    </div>
</div>

<table class='table table-striped dataTable dataTable-collapse'>
	<thead>
		<tr>
 			<th scope="col">Image (thumb)</th>
 			<th scope="col">Title</th>
 			<th scope="col">Category</th>
 			<th scope="col">Summary</th>
 			<th scope="col">Event Date</th>
 			<th scope="col">Publish Date</th>
 			<th scope="col">Remove Date</th>
 			<th scope="col">Last Modified</th>
 			<th scope="col">Modified By</th>
			<th scope="col">Order</th>
 			<th scope="col">Publish</th>
 			<th scope="col">Delete</th>
			<th scope="col">Options</th>
		</tr>
	</thead>
	<tbody>
	<?php
	$pages_module = new Model_Pages();
	?>
		<?php foreach ($news as $id => $news_item): ?>
			<tr>
				<td>
					<?php if (!empty($news_item['image'])): ?>
					<a href='<?php echo URL::Site('/admin/news/add_edit_item/' . $news_item['id']); ?>'>
						<img src="<?php echo Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$news_item['image'], 'news/_thumbs_cms');?>"
							 alt="<?=   (isset($news_item['alt_text']))   ? $news_item['alt_text']   : ''; ?>"
							 title="<?= (isset($news_item['title_text'])) ? $news_item['title_text'] : ''; ?>"
							 width="120" />
					</a>
					<?php endif; ?>
				</td>
				<td><a href='<?php echo URL::Site('/admin/news/add_edit_item/' . $news_item['id']); ?>'><?php echo $news_item['title']; ?></a></td>
				<td><a href='<?php echo URL::Site('/admin/news/add_edit_item/' . $news_item['id']); ?>'><?php echo $news_item['category']; ?></a></td>
				<td><a href='<?php echo URL::Site('/admin/news/add_edit_item/' . $news_item['id']); ?>'><?php echo Text::limit_chars($news_item['summary'],25," ...",true) ; ?></a></td>
				<td>
					<a href='<?php echo URL::Site('/admin/news/add_edit_item/' . $news_item['id']); ?>'>
						<?php echo (!empty($news_item['event_date']))? date('Y-m-d', strtotime($news_item['event_date'])) : '&nbsp;'; ?>
					</a>
				</td>
				<td><a href='<?php echo URL::Site('/admin/news/add_edit_item/' . $news_item['id']); ?>'><?php echo $news_item['date_publish']; ?></a></td>
		<!--		<td><a href='--><?php //echo URL::Site('/admin/news/add_edit_item/' . $news_item['id']); ?><!--'>--><?php //echo date('d-m-Y', strtotime($news_item['date_publish'])); ?><!--</a></td>-->
				<td><a href='<?php echo URL::Site('/admin/news/add_edit_item/' . $news_item['id']); ?>'><?php echo $news_item['date_remove']; ?></a></td>
		<!--		<td><a href='--><?php //echo URL::Site('/admin/news/add_edit_item/' . $news_item['id']); ?><!--'>--><?php //echo date('d-m-Y', strtotime($news_item['date_remove'])); ?><!--</a></td>-->
				<td><a href='<?php echo URL::Site('/admin/news/add_edit_item/' . $news_item['id']); ?>'><?php echo $news_item['date_modified']; ?></a></td>
				<td>
					<a href='<?php echo URL::Site('/admin/news/add_edit_item/' . $news_item['id']); ?>'>
						<?php echo $news_item['modified_by_role'].' '.$news_item['modified_by_name']; ?>
					</a>
				</td>
				<td><?= (($news_item['order'] == 0) ? '<span class="hidden">&#xe83a;</span>' : '').$news_item['order']?></td>
				<td id="publish_<?=$news_item['id']?>" class="publish" data-item_publish="<?php echo $news_item['publish'];?>">
					<span class="hidden"><?= $news_item['publish'] ?></span><?php echo (($news_item['publish'] == '1')? '<i class="icon-ok"></i>' : '<i class="icon-ban-circle"></i>')?>
				</td>
				<td id="delete_<?=$news_item['id']?>" class="delete"><i class="icon-remove-circle"></i></td>
				<td><a href="/news/<?php echo $pages_module->filter_name_tag($news_item['category']) . '/' . $pages_module->filter_name_tag($news_item['title']);?>">View</a></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

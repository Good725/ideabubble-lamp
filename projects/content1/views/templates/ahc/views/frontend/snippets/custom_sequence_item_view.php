<li class="cs_sequence_item">
	<?
	if ($item_data['link_type'] != 'none')
	{
		$link_url = '';
		if ($item_data['link_type'] == 'internal')
		{
			// Get the Details for the linked page
			$linked_page_page_tag = Model_Pages::get_page_by_id($item_data['link_url']);
			$link_url = URL::site().$linked_page_page_tag;
		}
		else
		{
			$link_url = $item_data['link_url'];
		}

		echo (trim($link_url) != '')? '<a href="'.$link_url.'" target="'.(($item_data['link_target'] == 0)? '_self' : '_blank').'" class="cs_item_link"></a>' : '';

	}
	?>
	<div class="cs_item_image">
		<img
			 src="<?=Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$item_data['image'], $item_data['image_location'])?>"
			 alt="<?= $item_data['image'] ?>">
	</div>

	<?php if ($item_data['html'] != ''): ?>
		<div class="bottom-content">
			<div class="row">
				<div class="medium-9 large-6 columns">
					<?= $item_data['html'] ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</li>
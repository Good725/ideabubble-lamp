<?
if ($item_data['link_type'] != 'none')
{
	$link_url = '';
	if ($item_data['link_type'] == 'internal')
	{
		// Get the details for the linked page
		$linked_page_page_tag = Model_Pages::get_page_by_id($item_data['link_url']);
		$link_url = URL::site().$linked_page_page_tag;
	}
	else
	{
		$link_url = $item_data['link_url'];
	}
	$link_url = trim($link_url);

}
?>
<li class="orbit-slide is-active">
	<?php if ($link_url): ?>
		<a href="<?= $link_url ?>">
	<?php endif; ?>
			<div class="orbit-image"
				 style="background-image: url('<?=Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$item_data['image'], $item_data['image_location'])?>');"
			>
			</div>
	<?php if ($link_url): ?>
		</a>
	<?php endif; ?>
	<div class="ib-orbit-caption">
		<div class="row">
			<div class="ib-orbit-summary">
				<h1 class="ib-orbit-summary-title"><?= $item_data['title'] ?></h1>

				<div class="ib-orbit-summary-text">
					<?= $item_data['html'] ?>
				</div>

				<?php if ($link_url): ?>
					<a class="button" href="<?= $link_url ?>"><?= __('More info') ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</li>

<?php include 'template_views/header.php' ?>

	<div class="content-wrapper content">
		<div class="col-xsmall-12"><?= trim($page_data['content']) ?><?php
			if (in_array($page_data['name_tag'], array('news', 'news.html')))
			{
				echo Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']);
			}else if(in_array($page_data['name_tag'], array('contact-us', 'contact-us.html'))){
				include 'template_views/form_quick_contact.php';
			}
			?></div>
	</div>

<?php include 'template_views/footer.php' ?>
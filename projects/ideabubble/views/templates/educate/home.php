<?php  include('template_views/header.php');?>
<!-- body start here -->
<div class="body-content">
	<!-- home page banner -->
	<?php require_once('template_views/home_banner.php');?>

	<!-- Key Features -->
	<?php //require_once('template_views/home_about.php'); ?>
	<?= $page_data['content'] ?>

	<!-- Why Choose Us? -->
	<?php //require_once('template_views/why_choose.php');?>

	<!-- wants to hire  -->
	<?php //require_once('template_views/wants_to_hire.php');?>
	
</div><!-- body-content ends-->
	
<!-- footer start here -->
<?php  require_once('template_views/footer.php');?>

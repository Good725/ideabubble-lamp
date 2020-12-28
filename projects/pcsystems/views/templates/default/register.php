<?php include 'template_views/html_document_header.php'; ?>
    <body id="<?=$page_data['layout']?>" class="<?=$page_data['category']?>">
    <!-- Wrapper -->
    <div id="wrapper" class="wrapper">
        <!-- Page -->
        <div id="page" class="page">
            <?php include 'template_views/header_view.php'; ?>

            <!-- Main Content -->
            <div id="main" class="main">
                <div id="sidebar" class="sidebar">
					<a href="#" id="sidebar-expand" class="sidebar-expand">
						<span class="sidebar-expand-icon">☰</span>
						<span class="sidebar-expand-text">Hide Options</span>
					</a>
					<div id="sidebar-inner" class="sidebar-inner">
						<div class="first left">
							<?php include 'template_views/menu_left_view.php'; ?>
							<?php include 'template_views/products_menu_view.php'; ?>
						</div>
					</div>
                </div>

                <div id="ct" class="ct">
                    <div id="banner" class="left">
                        <?php echo Model_PageBanner::render_frontend_banners($page_data['banner_photo']);  //Helper banners ?>
                    </div>
                    <div class="clear left"></div>
                    <div class="content">
						<div class="successful message_area">
							<?php $alerts = Session::instance()->get('messages'); ?>
							<?php if ( ! is_null($alerts)): ?>
								<?php foreach ($alerts as $alert): ?>
									<div class="alert alert-<?= $alert['type'] ?>">
										<a class="close" data-dismiss="alert">&times;</a>
										<strong><?= ucfirst($alert['type']) ?>:</strong> <?= $alert['content'] ?>
									</div>
								<?php endforeach; ?>
								<?php Session::instance()->delete('messages') ?>
							<?php endif; ?>
						</div>
                        <?php /*echo $page_data['content']; */?>
						<div class="breadcrumb-nav" id="breadcrumb-nav"><?= trim(''.IbHelpers::breadcrumb_navigation()) ?></div>
						<?php
						//Display Page Content
						echo $page_data['content'];
                        ?>
						<script>
						$("#register_account_type_other").prop("disabled", true);
						$("#register_account_type").on("change", function(){
							if(this.value == "Other"){
								$("#register_account_type_other").prop("disabled", false);
							} else {
								$("#register_account_type_other").prop("disabled",true);
							}
						});
						$("#account_registration_form").validationEngine();
						</script>
                    </div>
                </div>
            </div>
            <!-- /Main Content -->
            <?php include 'template_views/footer_view.php';?>
        </div>
        <!-- /Page -->
    </div>
    <!-- /Wrapper -->
    <?= Settings::instance()->get('footer_html'); ?>
    </body>
<?php include 'template_views/html_document_footer.php'; ?>
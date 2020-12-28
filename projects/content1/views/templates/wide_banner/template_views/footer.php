<div id="footer">
	<div class="footer-content">
		<div class="top">
			<!-- footer menu -->
			<div id="footer_menu">
				<?php menuhelper::add_menu_editable_heading('footer', 'footer_menu') ?>
			</div>
			<!-- /footer menu -->
			<div class="box2"><a href="/"><img src="<?= URL::get_skin_urlpath(TRUE) ?>images/footer_logo.gif" alt="logo" /></a></div>
		</div>

		<div class="footer_signature">
			<div class="left-part"><?= Settings::instance()->get('company_copyright') ?></div>
			<div class="right-part"><?= (Settings::instance()->get('cms_copyright') == '') ? 'Powered by <a href="https://ideabubble.ie">Idea Bubble</a> <a href="https://ideabubble.ie/website-cms-solutions.html">CMS</a>' : Settings::instance()->get('cms_copyright'); ?></div>
		</div>
	</div>
</div>

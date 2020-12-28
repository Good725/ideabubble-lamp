<!-- Footer -->
<?php
$telephone      = Settings::instance()->get('telephone');
$fax            = Settings::instance()->get('fax');
$mobile         = Settings::instance()->get('mobile');
$email          = Settings::instance()->get('email');
$address_line_1 = Settings::instance()->get('addres_line_1');
$address_line_2 = Settings::instance()->get('addres_line_2');
$address_line_3 = Settings::instance()->get('addres_line_3');
$address_line_4 = Settings::instance()->get('addres_line_4');
?>
<div id="footer" class="left">
    <div class="row_1">
        <div class="footer-top-line">
            <div class="footer-company-info-wrapper">
				<h3 class="title footer-company-info-title">Contact Us</h3>
				<ul class="footer-company-info">
					<li class="title"><?= Settings::instance()->get('company_title') ?></li>
					<?php if ($address_line_1 != ''): ?><li><?=$address_line_1 ?></li><?php endif; ?>
					<?php if ($address_line_2 != ''): ?><li><?=$address_line_2 ?></li><?php endif; ?>
					<?php if ($address_line_3 != ''): ?><li><?=$address_line_3 ?></li><?php endif; ?>
					<?php if ($address_line_4 != ''): ?><li><?=$address_line_4 ?></li><?php endif; ?>
					<?php if ($telephone != ''): ?>
						<li>Phone: <?=$telephone ?></li>
					<?php endif; ?>
					<?php if ($fax != ''): ?>
						<li>Fax: <?= Settings::instance()->get('fax'); ?></li>
					<?php endif; ?>
					<?php if ($mobile != ''): ?>
						<li>Mobile: <?= Settings::instance()->get('mobile'); ?></li>
					<?php endif; ?>
					<?php if ($email != ""): ?>
						<li>E-mail: <a href="mailto:<?= Settings::instance()->get('email'); ?>"><?= Settings::instance()->get('email'); ?></a></li>
					<?php endif; ?>
				</ul>
            </div>

			<div class="footer-info-wrapper">
				<?= menuhelper::add_menu_editable_heading('footer', 'footer-info'); ?>
			</div>
			<?php include 'form_view_newsletter_signup.php'; ?>

        </div>
    </div>

	<div class="row_2">
		<?php include 'social_media_view.php'; ?>
	</div>

    <div class="row_3">
        <span><?= Settings::instance()->get('company_copyright') ?></span>
        <div class="powered_by right">
            <?= (Settings::instance()->get('cms_copyright') == '')
                ? 'Powered by <a href="https://ideabubble.ie">Idea Bubble</a> <a href="https://ideabubble.ie/website-cms-solutions.html">CMS</a>'
                : Settings::instance()->get('cms_copyright'); ?>
        </div>
    </div>
</div>
<!-- /Footer -->

<!-- Footer -->
<?php
$telephone = Settings::instance()->get('telephone');
$fax = Settings::instance()->get('fax');
$mobile = Settings::instance()->get('mobile');
$email = Settings::instance()->get('email');
?>
<div id="footer" class="left">
    <div class="row_1">
        <div class="footer-top-line">
            <ul class="footer-company-info">
                <li class="title">PC Systems Limited</li>
                <li>29 Doughcloyne Court</li>
                <li>Industrial Estate</li>
                <li>Sarsfield road</li>
                <li>Cork city</li>
                <li>Ireland</li>
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
            <ul class="footer-products">
                <li class="title">PRODUCTS</li>
                <li><a href="">Components</a></li>
                <li><a href="">Computers</a></li>
                <li><a href="">Entertainment</a></li>
                <li><a href="">Peripherals</a></li>
                <li><a href="">Networking</a></li>
                <li><a href="">Supplies</a></li>
            </ul>

			<?= menuhelper::add_menu_editable_heading('footer', 'footer-info'); ?>

        </div>
		<?php
		if (Settings::instance()->get('newsletter_subscription_form') != 'FALSE')
		{
			include 'form_view_newsletter_signup.php';
		}
		?>
    </div>

	<div class="row_2">
		<div class="secure-online" id="secure-online">
			<img src="<?=URL::site()?>assets/default/images/visa-card-footer.png" alt="A wide range of cards accepted" title="A wide range of cards accepted">
		</div>
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

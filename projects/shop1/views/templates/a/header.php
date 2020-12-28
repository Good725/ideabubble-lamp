<?php
$logged_in_user = Auth::instance()->get_user();

$tripadvisor_url = Settings::instance()->get('tripadvisor_url');
if (trim($tripadvisor_url) != '' AND strpos($tripadvisor_url, 'tripadvisor.ie/') == FALSE)
{
	$tripadvisor_url = 'https://tripadvisor.ie/'.$tripadvisor_url;
}

?>
	<div id="header">
		<div class="header-options">
			<span class="mini_cart_wrapper" id="mini_cart_wrapper"><?php include "mini_cart.php"; ?></span>
			<?php if ($assets_folder_path == 24): ?>
				<div class="header_social" id="header_social">
					<?php if (Settings::instance()->get('facebook_url') != '') : ?>
						<span><a style="border:0;" href="https://www.facebook.com/<?= Settings::instance()->get('facebook_url'); ?>"><img src="/assets/<?= $assets_folder_path ?>/images/icons/facebook_icon.png"/></a></span>
					<?php endif; ?>
					<?php if (Settings::instance()->get('twitter_url') != '') : ?>
						<span><a style="border:0;" href="https://twitter.com/<?= Settings::instance()->get('twitter_url') ?>"><img src="/assets/<?= $assets_folder_path ?>/images/icons/twitter_icon.png"/></a></span>
					<?php endif; ?>
					<?php if ($tripadvisor_url != ''): ?>
						<a href="<?= $tripadvisor_url ?>" title="Tripadvisor"><img src="/assets/<?= $assets_folder_path ?>/images/icons/3_icon.png" alt="Tripadvisor"></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if (in_array($assets_folder_path, array(24, 25)) ): ?>
				<div class="header_btn_wrapper">
					<?php if($assets_folder_path == 24): ?>
						<?php if (Request::user_agent('mobile')): ?>
							<a href="tel: <?= Settings::instance()->get('mobile') ?>" class="phone_number">
								<img src="<?= URL::site().'assets/'.$assets_folder_path.'/images/header_phone.png'; ?>" alt="" />
								<span><?= Settings::instance()->get('mobile') ?></span>
							</a>
						<?php else: ?>
							<span class="phone_number">
								<img src="<?= URL::site().'assets/'.$assets_folder_path.'/images/header_phone.png'; ?>" alt="" />
								<span><?= Settings::instance()->get('mobile') ?></span>
							</span>
						<?php endif; ?>
						<a href="/contact-us.html" class="booking_btn">Make a Booking</a>
					<?php endif; ?>
					<?php if($assets_folder_path == 25): ?>
						<a href="/contact-us.html?contact_type=callback" class="button-callback">Request a Callback</a>
					<?php endif; ?>
				</div>
				<?php if($assets_folder_path == 25): ?>
					<a href="tel: <?= Settings::instance()->get('telephone') ?>" class="phone_number">
						<span><strong><?= Settings::instance()->get('telephone'); ?></strong></span>
					</a>
				<?php endif; ?>
			<?php endif; ?>

			<?php $allow_logins = Model_Plugin::is_enabled_for_role('Administrator', 'families') AND Model_Plugin::is_enabled_for_role('Administrator', 'bookings'); ?>
			<?php if ($allow_logins): ?>
				<div class="header-login-wrapper">
					<?php $logged_in_user = Auth::instance()->get_user(); ?>

					<?php if (empty($logged_in_user['id'])): ?>
						<a href="/admin/login" class="header-login header-login--in"><?= __('Log in') ?></a>
					<?php else: ?>
						<button type="button" class="header-login header-login--out" id="header-login-dropdown-toggle">
							<?= $logged_in_user['name'].' '.$logged_in_user['surname'] ?>
						</button>
						<div class="header-login-dropdown" id="header-login-dropdown">
							<a href="/admin/login/logout"><?= __('Log out') ?></a>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

		</div>

		<div id="header_logo">
			<a href="/"><img src="<?= $page_data['logo'] ?>" alt="<?= Settings::instance()->get('company_title') ?>"/></a>
			<?php
			$design_sign_button = URL::site().'assets/'.$assets_folder_path.'/images/design-your-sign-button.jpg';
			$header = @get_headers($design_sign_button);
			$design_sign_button_exists = (strpos($header[0],'404 Not Found') === FALSE);
			if ($design_sign_button_exists):
				?>
				<div id="header_design_your_sign">
					<a href="<?= URL::site(); ?>products.html/Design-Your-Sign">
						<img id="header_design-your-sign-button" src="<?= $design_sign_button ?>">
					</a>
				</div>
			<?php endif; ?>
		</div>

		<div id="header_slogan"><?= Settings::instance()->get('company_slogan'); ?></div>

		<?php if ($design_sign_button_exists): ?>
			<div class="header-buttons">
				<a href="/request-a-callback.html"><?= __('Request a Callback') ?></a>
				<a href="/contact-us.html"><?= __('Get a Quote') ?></a>
			</div>
		<?php endif; ?>

		<div id="main_menu"><?php menuhelper::add_menu_editable_heading('main') ?></div>
	</div>


<?php
// Autofill out formbuilder field, depending on URL query
if (isset($_GET['contact_type']))
{
	if ($_GET['contact_type'] == 'callback')
	{
		$preload_message = 'I am interested in your products. Please call me back.';
	}

	if (isset($preload_message))
	{
		$page_data['content'] = preg_replace(
			'#<textarea(.*name=\"contact_form_message\".*)>.*</textarea>#',
			'<textarea>'.$preload_message.'</textarea>',
			$page_data['content']);
	}
}
?>
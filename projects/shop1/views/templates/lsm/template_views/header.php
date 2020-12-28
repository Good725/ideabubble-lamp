<?php require_once('header_top.php');?>
		<header class="header">
			<div class="row">
				<div class="header-left">
					<a href="/" class="header-item header-logo">
						<img class="hidden--mobile" src="/assets/<?= $assets_folder_path ?>/images/logo.png" alt="<?= __('Home') ?>" />
						<img class="hidden--tablet hidden--desktop" src="/assets/<?= $assets_folder_path ?>/images/mob-logo.png" alt="<?= __('Home') ?>" />
					</a>
				</div>

				<div class="header-right">
                    <?php if ( ! empty($settings['course_apply_link']) AND trim($settings['course_apply_link'])): ?>
                        <div class="header-item header-action">
                            <a href="<?= trim($settings['course_apply_link']) ?>"<?= IbHelpers::is_external(trim($settings['apply_now_link'])) ? ' target="_blank"' : '' ?> class="button white--bg"><?= __('Apply Now') ?></a>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty($settings['frontend_heading_button_link']) AND trim($settings['frontend_heading_button_link'])): ?>
                        <div class="header-item header-action">
                            <a href="<?= trim($settings['frontend_heading_button_link']) ?>"<?= IbHelpers::is_external(trim($settings['frontend_heading_button_link'])) ? ' target="_blank"' : '' ?> class="button button--pay"><?= trim($settings['frontend_heading_button_text']) ?></a>
                        </div>
                    <?php endif; ?>

                    <?php if (Settings::instance()->get('frontend_login_link')): ?>
                        <div class="header-item header-action">
                            <?php if ( ! Auth::instance()->get_user()) { ?>
                                <a href="/admin" class="button button--continue"><?= __('Log in') ?></a>
                            <?php } else { ?>
                                <a href="/admin/login/logout" class="button button--continue"><?= __('Sign Out') ?></a>
                            <?php } ?>
                        </div>
                    <?php endif; ?>

					<div class="header-item header-menu-section">
						<a href="#" class="header-menu-expand">
							<span class="hidden--tablet hidden--desktop" title="<?= __('Info') ?>">
								<span class="fa fa-align-justify"></span>
							</span>

							<span class="hidden--mobile"><?= __('Info') ?></span>
						</a>
						<div class="header-menu header-menu--info">
							<div class="row header-menu-row">
								<?php menuhelper::add_menu_editable_heading('info') ?>
							</div>
						</div>
					</div>
				</div>

			</div>
		</header>

		<div class="quick_contact hidden--tablet hidden--desktop">
			<ul class="list-unstyled">
				<?php if ( ! empty($settings['telephone']) AND trim($settings['telephone'])): ?>
					<li class="quick_contact-item">
						<a href="tel:<?= str_replace(' ', '', $settings['telephone']) ?>">
							<span class="sr-only"><?= __('Phone') ?></span>
							<span class="fa fa-phone"></span>
						</a>
					</li>
				<?php endif; ?>
				<?php if ( ! empty($settings['email']) AND trim($settings['email'])): ?>
					<li class="quick_contact-item">
						<a href="mailto:<?= str_replace(' ', '', $settings['email']) ?>">
							<span class="sr-only"><?= __('Email') ?></span>
							<span class="fa fa-envelope"></span>
						</a>
					</li>
				<?php endif; ?>
				<li class="quick_contact-item">
					<a href="/contact-us.html#content_start">
						<span class="sr-only"><?= __('Location') ?></span>
						<span class="fa fa-map-marker"></span>
					</a>
				</li>
			</ul>
		</div>

		<div class="content">
			<?php include 'banner.php'; ?>

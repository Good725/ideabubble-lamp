		</main>
		<footer>
			<?php if ($page_data['layout'] == 'home'): ?>
				<div class="footer-panels">
					<div class="row">
						<div class="small-12 columns">
							<div class="content-container">
								<div class="row">
									<?php
									$panel_model = new Model_Panels();
									$pages_model = new Model_Pages();
									$footer_panels = $panel_model->get_panels('footer', (Settings::instance()->get('localisation_content_active') == '1'));
									?>

									<?php foreach ($footer_panels as $panel): ?>

										<?php
										if ($panel['link_id'] != '0' AND ! empty($panel['link_id']))
										{
											$page = $pages_model->get_page_data( $panel['link_id'] );
											$panel['link_url'] = (isset($page[0]['name_tag'])) ? '/'.$page[0]['name_tag'] : $panel['link_url'];
										}
										?>

										<div class="medium-4 <?= strpos($panel['text'], '{newsfeed-') ? 'ib-news-panel' : 'ib-panel' ?> columns">
											<?php if (trim($panel['link_url'])): ?><a href="<?= $panel['link_url'] ?>"><?php endif; ?>

												<h1><?= $panel['title'] ?></h1>
												<?= IbHelpers::expand_short_tags($panel['text']) ?>
												<?php if (trim($panel['image'])): ?>
													<img src="<?= $panel_path.$panel['image'] ?>" alt="" />
												<?php endif; ?>

												<?php if (trim($panel['link_url'])): ?></a><?php endif; ?>
										</div>

									<?php endforeach; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<div class="backdrop">
				<?php
				$company_title = trim(Settings::instance()->get('company_title'));
				$email         = trim(Settings::instance()->get('email'));
				$telephone     = trim(Settings::instance()->get('telephone'));
				$mobile        = trim(Settings::instance()->get('mobile'));
				$fax           = trim(Settings::instance()->get('fax'));
				$copyright     = trim(Settings::instance()->get('company_copyright'));
				?>
				<div class="footer-contact text-center">
					<?php if ($company_title.$email): ?>
						<dl>
							<?php if ($company_title): ?>
								<dd class="footer-contact-value"><?= $company_title ?></dd>
							<?php endif; ?>

							<?php if ($email): ?>
								<dt class="footer-contact-label"><?= __('Email') ?></dt>
								<dd class="footer-contact-value"><?= $email ?></dd>
							<?php endif; ?>
						</dl>
					<?php endif; ?>

					<?php if ($telephone.$mobile.$fax): ?>
						<dl>
							<?php if ($telephone): ?>
								<dt class="footer-contact-label"><?= __('Telephone') ?></dt>
								<dd class="footer-contact-value"><?= $telephone ?></dd>
							<?php endif; ?>

							<?php if ($mobile): ?>
								<dt class="footer-contact-label"><?= __('Mobile') ?></dt>
								<dd class="footer-contact-value"><?= $mobile ?></dd>
							<?php endif; ?>

							<?php if ($fax): ?>
								<dt class="footer-contact-label"><?= __('Fax') ?></dt>
								<dd class="footer-contact-value"><?= $fax ?></dd>
							<?php endif; ?>
						</dl>
					<?php endif; ?>

					<?php if ($copyright): ?>
						<dl>
							<dd class="footer-contact-value"><?= $copyright ?></dd>
						</dl>
					<?php endif; ?>

				</div>
			</div>

		</footer>

		<?php // JS ?>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/foundation.min.js"></script>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/jquery.validationEngine2.js"></script>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/jquery.validationEngine2-en.js"></script>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/general.js"></script>

		<?= settings::get_google_analitycs_script(); ?>

		<?= Settings::instance()->get('footer_html') ?>

	</body>
</html>
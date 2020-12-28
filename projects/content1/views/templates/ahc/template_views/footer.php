		<footer>
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

								<div class="medium-3 columns">
									<?php if (trim($panel['link_url'])): ?><a href="<?= $panel['link_url'] ?>"><?php endif; ?>

										<h3><?= $panel['title'] ?></h3>
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
		</footer>

		<?php // JS ?>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/foundation.min.js"></script>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/jquery.validationEngine2.js"></script>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/jquery.validationEngine2-en.js"></script>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/general.js"></script>
		<script type="text/javascript">
			/* <![CDATA[ */
			var google_conversion_id = 972662554;
			var google_custom_params = window.google_tag_params;
			var google_remarketing_only = true;
			/* ]]> */
		</script>
		<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
		</script>
		<noscript>
			<div style="display:inline;">
				<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/972662554/?value=0&amp;guid=ON&amp;script=0"/>
			</div>
		</noscript>

		<?= settings::get_google_analitycs_script(); ?>

		<?= Settings::instance()->get('footer_html') ?>

	</body>
</html>
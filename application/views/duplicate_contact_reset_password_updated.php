<div class="Bg-wrapper">
	<div class="middle-align">
		<div class="row">
			<div class="small-container small-width">
				<div class="theme-form">
					<div class="inn-logo">
						<a href="#"><img src="<?php echo URL::get_engine_theme_assets_base().'img/inn-logo.png';?>"></a>
					</div>
					<form>
						<div class="alert alert-success">
							<a href="#" class="close-btn"><i class="fa fa-times" aria-hidden="true"></i></a>
							<strong>Success:</strong>  Your new credentials have been updated.
						</div>
						 <div class="form-wrap align-center">
							<a href="/admin/login" class="blueBtn">Login</a>
						</div>
					</form>

				</div>
				<div class="green-strip">
						Need an account? <a href="/admin/login/register">Sign up</a>
				</div>
				<div class="bottom-section">
					<?php if (!empty($footer_links)) { ?>
						<ul class="usefull-links">
							<?php foreach ($footer_links as $link): ?>
								<li>
									<a href="/<?= $link['name_tag'] ?>"><?= $link['title'] ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php } else { ?>
						<div class="poweredby"><p><?= Settings::instance()->get('cms_copyright') ?></p></div>
					<?php } ?>
				</div>

			</div>
		</div>
	</div>
</div>

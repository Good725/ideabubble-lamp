<div class="Bg-wrapper">
	<div class="middle-align">
		<div class="row">
			<div class="small-container small-width">
				<div class="theme-form">
					<div class="inn-logo">
						<a href="#"><img src="<?php echo URL::get_engine_theme_assets_base().'img/inn-logo.png';?>"></a>
					</div>
					<form>
						<div class="alert alert-info">
							<a href="#" class="close-btn"><i class="fa fa-times" aria-hidden="true"></i></a>
							<strong>Attention:</strong> Mobile phone doesn’t match to this e-mail address.
						</div>
						<script>
							$(".alert .close-btn").on("click", function(){
								$(this).parent().hide();
							});
						</script>
						<p>Please contact the office to continue with this signup or login.</p> 
						<p class="phone-num"><i class="fa fa-phone" aria-hidden="true"></i><a href="tel:44 84 95 654">+44 84 95 654</a></p>
						<div class="or-text"><span>Or</span></div>
						<div class="ret-page"><a href="javascript: history.back()">Return to previous page to enter correct details</a></div>
						<p class="align-center"> 
							<a href="/admin/login/forgot_password">Forgot your password?</a>&nbsp;&nbsp;<a href="/admin/login">Can't log in?</a>
						</p>                   
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

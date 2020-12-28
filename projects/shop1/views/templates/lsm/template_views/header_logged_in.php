<?php require_once('header_top.php'); ?>
		<div class="db-top-header clear">
			<div class="top-header-row row">
				<div class="top-h-logo">
					<a class="db-toggle-menu" href="javascript:void(0)">
						<i class="fa fa-bars" aria-hidden="true"></i>
					</a>
					<div class="db-logo">
						<img src="/assets/<?= $assets_folder_path ?>/images/db-logo.png" alt="<?= __('Home') ?>" />
					</div>
				</div>
				<div class="db-right-notification">
					<ul class="db-notifications">
<!--						<li><a class="green-icon" href="javascript:void(0)"><i class="fa fa-heart" aria-hidden="true"></i><span class="counter">5</span></a></li>-->
<!--						<li><a class="white-icon" href="javascript:void(0)"><i class=" fa fa-shopping-cart" aria-hidden="true"></i> <span class="counter">5</span></a></li>-->
						<li>
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
						</li>
						<li class="avatar">
							<?php if ( ! empty($user['id'])): ?>
								<a href="/admin/profile/edit?section=contact" class="header-avatar">
									<img src="<?= URL::get_avatar($user['id']); ?>" alt="profile" width="50" height="50" title="<?= __('Profile: ').$user['name'] ?>" />
								</a>
							<?php endif; ?>
							<div class="subMenu-link">
								<ul>
									<li><a href="/frontend/contacts3">Profile</a></li>
									<li><a href="/admin/login/logout">Log out</a></li>
								</ul>
							</div>

						</li>
					</ul>
				</div>
				<div class="db-center-btns">
					<ul>
						<li>
							<a href="#" class="header-menu-expand">Learn</a>
							<div class="header-menu header-menu--learn">
								<div class="row header-menu-row">
									<a class="menu-expand"></a>
									<?php menuhelper::add_menu_editable_heading('learn') ?>
								</div>
							</div>
						</li>
						<li><a class="tempBtn" href="/course-list.html">Browse courses</a></li>
					</ul>
				</div>
			</div>
		</div>

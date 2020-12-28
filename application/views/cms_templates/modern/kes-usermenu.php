<?php
	$assets_folder_path = Kohana::$config->load('config')->assets_folder_path;
	$logged_in_user = Auth::instance()->get_user();
?>
<div class="db-top-header clear">
	<div class="top-header-row row">
		<div class="top-h-logo logo-dropdown">
			<button class="sidebar-toggle" id="sidebar-toggle">
				<i class="icon icon-bars" aria-hidden="true"></i>
			</button>
			<a href="/admin">
				<div class="db-logo">
					<img src="/assets/<?= $assets_folder_path ?>/images/db-logo.png" alt="<?= __('Home') ?>" />
				</div>
			</a>

			
			<ul>
				<? /* Temporary, until this is database driven */ ?>
				<?php if (Cookie::get($logged_in_user['id'].'_application') == 'ibeducate'): ?>
					<li><a href="/admin/contacts3">Contacts</a></li>
					<li><a href="/admin/bookings">Bookings</a></li>
				<?php else: ?>
					<?= implode(' ', $header->menu) ?>
				<?php endif; ?>
			</ul>

		</div>
		<div class="db-right-notification">
			<ul class="user-tools db-notifications">
				<!-- heart icon start here -->
				<?php if (Auth::instance()->has_access('user_tools_messages')): ?>
					<?php if (class_exists('Controller_Admin_Messaging')): ?>
						<li class="user-tools-messages">
							<div class="dropdown">
								<a href="#" class="user-tools-link white-icon" id="user-tools-messaging-expand" data-toggle="" aria-haspopup="true" aria-expanded="false">
									<span class="user-tools-icon messages_icon fa  icon-bell" title="<?= __('Messages') ?>"></span>
									<span class="user_tools_notification_amount" id="message-notifications-amount"></span>
								</a>
								<div class="dropdown-menu pull-right user-notifications-dropout" id="user-notifications-dropout" aria-labelledby="user-tools-messaging-expand">
									<aside class="bulletin-menu-wrapper">
										<div class="bulletin-header">
											<ul class="nav-tabs clearfix">
												<li class="active">
													<a href="#bulletin-tab-pane-messaging"  role="tab" id="bulletin-tab-messaging"  data-toggle="tab" aria-controls="bulletin-tab-pane-messaging">Messages</a>
												</li>
												<li>
													<a href="#bulletin-tab-pane-activities" role="tab" id="bulletin-tab-activities" data-toggle="tab" aria-controls="bulletin-tab-pane-activities">Latest Activity</a>
												</li>
												<?php if (Auth::instance()->has_access('contacts2_edit')): ?>
													<li>
														<a href="#bulletin-tab-pane-contacts"   role="tab" id="bulletin-tab-contacts"   data-toggle="tab" aria-controls="bulletin-tab-pane-contacts">Contacts</a>
													</li>
												<?php endif; ?>
											</ul>
										</div>

										<div class="tab-content">
											<div role="tabpanel" class="tab-pane fade active in" id="bulletin-tab-pane-messaging" aria-labelledby="bulletin-tab-messaging">
												<ul class="user-notifications-dropout" id="user-notifications-dropout">
													<li id="message-notification-list"></li>
													<li id="message-notification-view_message"></li>
												</ul>
											</div>
											<div role="tabpanel" class="tab-pane fade" id="bulletin-tab-pane-activities" aria-labelledby="bulletin-tab-activities">
												<div class="bulletin-activity-wrapper">
													<h3><a href="/admin/settings/activities">Latest Activity</a></h3>
													<div class="bulletin-activity-list-wrapper">
														<ul class="bulletin-activity-list">
															<?php
															if(isset($latest_activity) && !empty($latest_activity)):
																foreach ($latest_activity as $activity): ?>
																	<li class="bulletin-activity-item"
																		data-user_id="<?= $activity['user_id'] ?>"
																		data-action="<?= $activity['action'] ?>"
																		data-item_type="<?= $activity['item_type'] ?>"
																		data-item_id="<?= $activity['item_id'] ?>"
																		>
																		<time class="bulletin-activity-time" title="<?= $activity['timestamp'] ?>" datetime="<?= $activity['timestamp'] ?>"><span><?= IbHelpers::relative_time($activity['timestamp']) ?></span></time>
																		<p class="bulletin-activity-summary">
																			<span class="bulletin-activity-subject"><?= $activity['firstname'].' '.$activity['surname'] ?></span>
																			<span class="bulletin-activity-verb"><?= IbHelpers::verb_past_tense(strtolower($activity['action_name'])) ?></span>
																			<?php if ( ! in_array($activity['action'], array('login', 'logout'))): ?>
																				<span class="bulletin-activity-object">a <?= strtolower($activity['item_type_name']) ?></span>
																			<?php endif; ?>
																		</p>
																	</li>
																<?php endforeach; 
															endif;
															?>
														</ul>
													</div>
												</div>
											</div>

											<?php if (Auth::instance()->has_access('contacts2_edit')): ?>
												<div role="tabpanel" class="tab-pane fade" id="bulletin-tab-pane-contacts" aria-labelledby="bulletin-tab-contacts">
													<div class="bulletin-contacts-wrapper">
														<?php
														// todo: move to controller
														$users = ORM::factory('User')->where('deleted', '=', 0)->order_by('last_login', 'desc')->find_all();
														?>
														<div class="bulletin-contacts-list-wrapper">
															<ul class="bulletin-contacts-list">
																<?php foreach ($users as $user): ?>
																	<?php
																	$user_name = trim($user->name.' '.$user->surname);
																	$user_name = $user_name ? $user_name : $user->email;
																	?>
																	<li class="bulletin-contact">
																		<a href="#">
																			<span class="bulletin-contact-name"><?= $user_name ?></span>
																	<span class="bulletin-contact-lastlogin">
																		Logged in
																		<time datetime="<?= date('Y-m-d H:i:s', $user->last_login)?>"
																			  title="<?= date('Y-m-d H:i:s', $user->last_login)?>"
																			><?= IbHelpers::relative_time($user->last_login)?></time>
																	</span>
																		</a>
																	</li>
																<?php endforeach; ?>
															</ul>
														</div>
													</div>
												</div>
											<?php endif; ?>
										</div>
									</aside>
								</div>
							</div>
						</li>
					<?php endif; ?>
				<?php endif; ?>
				<!-- heart icon end here -->

				<!-- help icon start here  -->
				<?php if (Auth::instance()->has_access('user_tools_help')): ?>
                <?php $help_links = explode("\n", trim(Settings::instance()->get('help_links'))); ?>
                <?php if (sizeof($help_links) > 0 AND $help_links[0] != ''): ?>
                    <li class="user-tools-help">
                        <a href="#" class="dropdown-toggle header-menu-expand user-tools-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="user-tools-icon icon-question" title="<?= __('Help') ?>"></span>
                            <span class="user-tool-text"><?= __('Help') ?></span>
                        </a>
                        <ul class="dropdown-menu pull-right" aria-labelledby="user-tools-profile-expand">
                            <?php foreach($help_links as $link): ?>
                                <?php
                                $url = substr($link, strpos($link,'{')+1);
                                $url = substr($url,0,strpos($url,'}'));
                                $href = (strpos($url, "http://") === 0 OR strpos($url, "https://") === 0 )? $url : URL::site($url);
                                ?>
                                <li><a href="<?=$href;?>"
                                       target="<?= strpos($link,'[')!=-1 ? substr($link,strpos($link,'['),strpos($link,']')):'_self'; ?>">
                                        <?= __(substr($link,0,strpos($link,'{')));?>
                                    </a>
                                </li>
                            <?php endforeach; // strpos($link,'}')?>
                        </ul>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
				<!-- help icon end here  -->

				<!-- user tool start here -->
				<li class="user-tools-avatar">
					<div class="dropdown">
						<a href="#" class="dropdown-toggle user-tools-link" id="user-tools-profile-expand" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<img class="profile-section-avatar" src="<?= URL::get_avatar($logged_in_user['id']); ?>" alt="profile" title="<?= __('Profile: ').$logged_in_user['name'] ?>" />
						</a>
						<ul class="dropdown-menu pull-right user-tools-messaging-dropdown-menu" aria-labelledby="user-tools-profile-expand">
							<?php $divider = FALSE;?>
							<?php if(Auth::instance()->has_access('user_profile')): $divider = TRUE; ?>
								<li<?= (isset($current_controller) AND $current_controller == 'profile') ? ' class="active"' : '' ?>><a href="/admin/profile/edit?section=contact">Profile</a></li>
							<?php endif; ?>
							<?php if (Auth::instance()->has_access('settings')) { ?>
								<li data-controller="settings" class="sidebar-menu-li<?= (isset($current_controller) AND $current_controller == 'settings' AND $current_action == 'activities') ? ' sidebar-menu-li--current active' : '' ?>">
									<a href="/admin/settings/activities" title="<?= __('System') ?>">
										<?= __('System') ?>
									</a>
								</li>
								<li data-controller="settings" class="sidebar-menu-li<?= (isset($current_controller) AND $current_controller == 'settings' AND $current_action != 'activities') ? ' sidebar-menu-li--current active' : '' ?>">
									<a href="/admin/settings" title="<?= __('Settings') ?>">
										<?= __('Settings') ?>
									</a>
								</li>
								<li data-controller="usermanagement" class="sidebar-menu-li<?= (isset($current_controller) AND $current_controller == 'usermanagement') ? ' sidebar-menu-li--current active' : '' ?>">
									<a href="/admin/usermanagement/users" title="<?= __('User Management') ?>">
										<?= __('User Management') ?>
									</a>
								</li>
							<?php } ?>
							<?php
								if(isset($header->notification_links)):
							?>
								<?= implode(' ', $header->notification_links); ?>
							<?php endif; ?>
							<?php if($divider): ?>
								<li role="presentation" class="divider"></li>
							<?php endif; ?>
							<li><a href="/admin/login/logout"><?= __('Log out') ?></a></li>
						</ul>
					</div>
				</li>
				<!-- user tool end here -->
				
			</ul>

			<!-- right side chat panel -->
			<div class="user-msg-wrapper">
				<span class="user-chat-dropdown-toggle"><i class="icon icon-ellipsis-h" aria-hidden="true"></i></span>
				<div class="user-chat-dropdown">
				<?php
					
					function array_swap(&$array,$swap_a,$swap_b)
					{
					   list($array[$swap_a],$array[$swap_b]) = array($array[$swap_b],$array[$swap_a]);
					}
					$array_files = array();
					foreach ($GLOBALS['ibcms_right_panels'] as $rwidget) {
						if (isset($rwidget['view'])) {
							$array_files[] = $rwidget['view'][0];

							/*foreach ($rwidget['view'] as $rwidget_view) {
								include $rwidget_view;
							}*/
						}
					}
					if(!empty($array_files))
					{
						if(count($array_files) >= 3)
						{
							array_swap($array_files, 1, 2);
						}
						foreach ($array_files as $rwidget_view) {
							include $rwidget_view;
						}
					}
				?>
				</div>
			</div>		
		</div>	


		<div class="db-center-btns">
			<ul>			
				<li>
					<?php
					$dashboard_user = Auth::instance()->get_user();
					if (isset($header->available_dashboards) AND count($header->available_dashboards) > 0  && Auth::instance()->has_access('dashboards')):
					?>
						<div id="dashboards-usermenu-btn" class="left">
							<div class="dropdown">
								<button class="btn btn-default dropdown-toggle dashboards-dropdown-btn" type="button" data-toggle="dropdown"><?= __('Dashboards') ?>
									<span class="caret"></span></button>
								<ul class="dropdown-menu">
									<?php foreach ($header->available_dashboards as $available_dashboard): ?>
										<li><a href="/admin/dashboards/view_dashboard/<?= $available_dashboard['id'] ?>"><?= $available_dashboard['title'] ?></a></li>
									<?php endforeach; ?>
									<?php if (Model_Plugin::get_isplugin_enabled_foruser($dashboard_user['role_id'], 'dashboards')): ?>
										<li role="presentation" class="divider"></li>
										<li><a href="/admin/dashboards"><?= __('Manage Dashboards') ?></a></li>
									<?php endif; ?>
								</ul>
							</div>			
						</div>
					<?php endif; ?>
				</li>
			</ul>
		</div>
	</div>
</div>

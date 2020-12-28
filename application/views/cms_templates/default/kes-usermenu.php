<?php
	$assets_folder_path = Kohana::$config->load('config')->assets_folder_path;
	$logged_in_user = Auth::instance()->get_user();
?>
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
				<li><a class="green-icon" href="javascript:void(0)"><i class="fa icon-heart" aria-hidden="true"></i><span class="counter">5</span></a></li>
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

				<li><a class="white-icon" href="javascript:void(0)"><i class=" fa icon-shopping-cart" aria-hidden="true"></i> <span class="counter">5</span></a></li>
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
										<li data-controller="settings" class="sidebar-menu-li<?= (isset($current_controller) AND $current_controller == 'settings') ? ' sidebar-menu-li--current active' : '' ?>">
											<a href="/admin/settings" title="<?= __('Settings') ?>">
												<?= __('Settings') ?>
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
							<li><a href="/admin/login/logout"><?= __('Logout') ?></a></li>
						</ul>
					</div>
				</li>
				<!-- user tool end here -->
				
			</ul>
		</div>	
			
		<div class="db-center-btns">
			<ul>
				<?php if (isset($available_dashboards) AND count($available_dashboards) > 0): ?>
					<li>
						<a href="#" class="header-menu-expand">Dashboards</a>
						<div class="header-menu header-menu--learn">
							<div class="row header-menu-row">
								<a class="menu-expand"></a>
								<ul class="learn">
									<?php foreach ($available_dashboards as $available_dashboard): ?>
										<li><a href="/admin/dashboards/view_dashboard/<?= $available_dashboard['id'] ?>"><?= $available_dashboard['title'] ?></a></li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
					</li>
				<?php endif; ?>
			</ul>
		</div>		
	</div>
</div>

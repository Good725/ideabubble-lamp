<div class="message-wrapper">
	<div class="container">
		<div class="message-close">
			<a href="javascript:void(0)" class="popup-close" data-popup-close="popup-email"><i class="fa icon-times" aria-hidden="true"></i> 
				<span>ESC</span>
			</a>
			
		</div>
		<div class="messaging-sidebar-columns">
			<div class="messaging-sidebar-column message--nav">
				<h3>Michael O’Callaghan <a class="pullBtn" href="javascript:void(0);"><i class="fa icon-plus-circle" aria-hidden="true"></i></i></a>
					<ul class="toggle-box mail--list">
						<li><a class="detail-btn" href="javascript:void(0)" rel="template">Template <i class="fa icon-check" aria-hidden="true"></i></a></li>
						<li><a class="detail-btn" href="javascript:void(0)" rel="send-email">Compose Email <i class="fa icon-check" aria-hidden="true"></i></a></li>
						<li><a  href="#">Compose SMS <i class="fa icon-check" aria-hidden="true"></i></a></li>
						<li><a  href="#">Compose Alert <i class="fa icon-check" aria-hidden="true"></i></a></li>
					</ul>
				</h3>
				<ul class="">
					 <?php    
						$i = 0;
						if(!isset($submenu_message) || empty($submenu_message))
						{
							$submenu_message = $header->submenu_message;
						}
						$classes[] = '<i class="fa icon-envelope-o" aria-hidden="true"></i> <span class="counts">5</span>';
						$classes[] = '<i class="fa icon-paper-plane" aria-hidden="true"></i> <span class="counts">3</span>';
						$classes[] = '<i class="fa icon-star" aria-hidden="true"></i>';
						$classes[] = '<i class="fa icon-paper-plane" aria-hidden="true"></i>';
						$classes[] = '<i class="fa icon-calendar" aria-hidden="true"></i>';
						$classes[] = '<i class="fa icon-file-text-o" aria-hidden="true"></i>';
						$classes[] = '<i class="fa icon-bug" aria-hidden="true"></i>';
						$classes[] = '<i class="fa icon-users" aria-hidden="true"></i>';
						$classes[] = '<i class="fa icon-trash" aria-hidden="true"></i>';
						$classes[] = '<i class="fa icon-cog" aria-hidden="true"></i>';
						$classes[] = '<i class="fa icon-wrench" aria-hidden="true"></i>';
						foreach ($submenu_message as $entry)
						{
							echo '<li><a href="' . URL::Site( $entry['url']) . '">' .$classes[$i] . __($entry['name']) . '</a></li>';
							$i++;
						}
					?>
					<li class="dropdown-nav"><a class="pullBtn" href="javascript:void(0)">Templates</a>
						<ul class="toggle-box sub--nav">
							<li><a href="#">Template 1</a></li>
							<li><a href="#">Template 2</a></li>
							<li><a href="#">Template 3</a></li>
							<li><a href="#">Template 4</a></li>
						</ul>
					</li>
				</ul>
			</div> 
			<div class="messaging-sidebar-column grayBg">
				<div class="search-wrap">
					<input type="text" placeholder="Search">
					<button class="search-btn">
						<i class="fa icon-search" aria-hidden="true"></i>
					</button>
				</div>
				<div class="body-area">
					<div class="toptitle">
						<div class="action">
                            <?= Form::ib_checkbox(__('Select All'), NULL, NULL, FALSE, array('class' => 'checked-all')); ?>
						</div>
						<div class="filter">
							<a href="javascript:void(0);" class="pullBtn">Filter <span class="fa icon-angle-down" aria-hidden="true"></span></a>
							<ul class="toggle-box filter--nav">
								<li><a href="#">Email + SMS <span class="fa icon-check" aria-hidden="true"></span></a></li>
								<li><a href="#">SMS <i class="fa icon-check" aria-hidden="true"></i></a></li>
								<li><a href="#" class="active">E-mail <i class="fa icon-check" aria-hidden="true"></i></a></li>
							</ul>
						</div>
						<ul class="messaging-sidebar-actions">
							<li><a href="#"><i class="fa icon-trash" aria-hidden="true"></i></a></li>
							<li><a href="#"><i class="fa icon-bug" aria-hidden="true"></i></a></li>
							<li><a href="#"><i class="fa icon-star-o" aria-hidden="true"></i></a></li>
							<li><a href="#"><i class="fa icon-envelope" aria-hidden="true"></i></a></li>
							<li><a href="#"><i class="fa icon-envelope-open" aria-hidden="true"></i></a></li>
						</ul>
					</div>                        
					<ul class="medialist">
						<li class="unread">
							<div class="grid first">
								<ul class="user-action">
									<li><i class="fa icon-envelope" aria-hidden="true"></i></li>
									<li><input type="checkbox" class="input-field"></li>
									<li><i class="fa icon-star-o" aria-hidden="true"></i></li>
								</ul>
							</div>
							<div class="grid second">
								<h4>Maja Otic</h4>
								<p>Subject Line</p>
							</div>
							<span class="grid third">08:00</span>
						</li>
						<li class="unread">
							<div class="grid first">
								<ul class="user-action">
									<li><i class="fa icon-mobile" aria-hidden="true"></i></li>
									<li><input type="checkbox" class="input-field"></li>
									<li><i class="fa icon-star-o" aria-hidden="true"></i></li>
								</ul>
							</div>
							<div class="grid second">
								<h4>Stephen King</h4>
								<p>Subject Line</p>
							</div>
							<span class="grid third">Yesterday</span>
						</li>
						<li>
							<div class="grid first">
								<ul class="user-action">
									<li><i class="fa icon-envelope" aria-hidden="true"></i></li>
									<li><input type="checkbox" class="input-field"></li>
									<li class="starred"><i class="fa icon-star-o" aria-hidden="true"></i></li>
								</ul>
							</div>
							<div class="grid second">
								<h4>Maja Otic</h4>
								<p>Subject Line</p>
							</div>
							<span class="grid third">10/19</span>
						</li>
						<li>
							<div class="grid first">
								<ul class="user-action">
									<li><i class="fa icon-envelope" aria-hidden="true"></i></li>
									<li><input type="checkbox" class="input-field"></li>
									<li class="starred"><i class="fa icon-star-o" aria-hidden="true"></i></li>
								</ul>
							</div>
							<div class="grid second">
								<h4>Maja Otic</h4>
								<p>Subject Line</p>
							</div>
							<span class="grid third">10/19</span>
						</li>
						<li>
							<div class="grid first">
								<ul class="user-action">
									<li><i class="fa icon-envelope" aria-hidden="true"></i></li>
									<li><input type="checkbox" class="input-field"></li>
									<li><i class="fa icon-star-o" aria-hidden="true"></i></li>
								</ul>
							</div>
							<div class="grid second">
								<h4>Bernadette O’Connor</h4>
								<p>Subject Line</p>
							</div>
							<span class="grid third">10/19</span>
						</li>
					</ul>
				</div>
				<div class="pagination-wrap">
					<span class="grid-1">Show 4 of 1267 items</span>
					<ul class="pagination-btn">
						<li><a href="#"><i class="fa icon-angle-left" aria-hidden="true"></i></a></li>
						<li><a href="#"><i class="fa icon-angle-right" aria-hidden="true"></i></a></li>
					</ul>
				</div>

			</div>
			<div class="messaging-sidebar-column">
				<!-- mail wrap -->
				<?php require_once('mail_wrap.php');?>

				<!-- create edit template -->
				<?php require_once('create_edit_template.php');?>

				<!-- send email -->
				<?php require_once('send_email.php');?>

			</div>
			<!-- read mail end -->
			<div class="messaging-sidebar-column last">
				<!-- detailed_status -->
				<?php require_once('detailed_status.php');?>
			</div>
		</div>
	</div>
	<!-- popup html -->
	<?php require_once('slider_popup.php');?>

<div class="well sidebar-nav">

	<?php if (isset($menus) && is_array($menus)) { ?>
	<ul class="nav nav-list">
		<? foreach ($menus as $group => $menu) { ?>
		<li class="nav-header"><?php echo $group; ?></li>

			<? foreach ($menu as $list) { ?>
				<li<?php
					$link = explode('/', $list['link']); // find the action from the url
					if (isset($link[2]) && $link[2] === $current_action OR (!isset($link[2]) && $current_action === 'index')) //if an action is set and it is in the url mark the page as current
					 echo  " class='active'"; ?>>
					<a href="<?php echo URL::Site($list['link']); ?>"><?php echo $list['name']; ?></a>
				</li>

			<?php } ?>
		<? } ?>
	</ul >
	<?php } ?>
</div>


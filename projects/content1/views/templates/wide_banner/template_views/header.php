<!-- header -->
<div class="header" id="header">
	<div class="logo">
		<a href="/">
			<img src="<?= URL::get_skin_urlpath(TRUE) ?>images/logo.gif" alt="Home" title="Home" />
		</a>
	</div>
	<div class="rt">
		<div class="top">
			Head Office
			<?php if ($settings['telephone'] != ''): ?>
				<span>Tel:</span> <?= $settings['telephone'] ?>  |
			<?php endif; ?>
			<?php if ($settings['fax'] != ''): ?>
				<span>Fax:</span> <?= $settings['fax'] ?>
			<?php endif; ?>
			<a href="/fdc-branches.html">Branch Locator</a>
		</div>

		<div class="clear">
			<a href="/contact-us.html" class="btn2">Contact Us &raquo;</a>
			<a href="/pay-online.html" class="btn1">Pay Direct Online</a>
		</div>
	</div>
</div>
<!-- /header -->

<!-- navcontainer -->
<div id="navcontainer" class="nav greedy-nav">
	<button type="button"><span class="nav-expand"></span></button>
	<?php menuhelper::add_menu_editable_heading('main','sf-menu&#32;visible-links'); ?>
	<ul class="sf-menu hidden-links hidden"></ul>
</div>
<!-- /navcontainer -->

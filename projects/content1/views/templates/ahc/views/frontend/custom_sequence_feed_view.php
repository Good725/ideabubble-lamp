<?php
if (isset($view_js_files))
{
	foreach ($view_js_files as $js_item_html) echo $js_item_html;
}

$browser_data = Request::user_agent(array('browser', 'version'));

if ($browser_data['browser'] = 'Internet Explorer' AND $browser_data['version'] < 9 AND $sequence_data['animation_type'] == 'fade')
{
	$sequence_data['animation_type'] = 'horizontal';
}
?>
<div class="cs_holder<?= (isset($sequence_holder_plugin)) ? ' ' . $sequence_holder_plugin : '' ?> hero-image">
	<ul id="cs_sequence_<?= $sequence_data['id'] ?>" class="cs_sequence">
		<?=(isset($sequence_items)) ? $sequence_items : ''?>
	</ul>
</div>

<script type="text/javascript">
	var cs_slider_<?= $sequence_data['id'] ?>;
	$(document).ready(function()
	{
		cs_slider_<?=$sequence_data['id']?> = $('#cs_sequence_<?=$sequence_data['id']?>').bxSlider({
			mode: '<?=$sequence_data['animation_type']?>',
			controls: <?=($sequence_data['controls'] == 1)? 'true' : 'false'?>,
			pager: <?=($sequence_data['pagination'] == 1)? 'true' : 'false'?>,
			speed: <?=$sequence_data['timeout']?>,
			auto: true
		});
	});

	$(window).load(function()
	{
		cs_slider_<?= $sequence_data['id'] ?>.reloadSlider();
	});

	$(window).resize(function()
	{
		cs_slider_<?= $sequence_data['id'] ?>.reloadSlider();

	});
</script>

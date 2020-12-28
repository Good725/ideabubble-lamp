<form action="<?=Request::initial()->uri()?>" method="get" class="paging-bl">
	<?php
	$uri = Request::initial()->uri();
	$pagination_parameters = Kohana::sanitize($_GET);
	$per_page = 12;

	foreach($pagination_parameters as $name => $value)
	{
		if ($name != 'page') // "page" value is submitted through the pagination buttons
		{
			if (is_array($value))
			{
				foreach($value as $name2 => $value2)
				{
					if (is_array($value2))
					{
						foreach ($value2 as $name3 => $value3)
						{
							echo '<input type="hidden" name="' . $name . '['. $name2 .']['. $name3 .']" value="' . $value3 . '" />';
						}
					}
					else
					{
						echo '<input type="hidden" name="' . $name . '['. $name2 .']" value="' . $value2 . '" />';
					}
				}
			}
			else
			{
				echo '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
			}
		}
	}
	unset($pagination_parameters['page']);
	$start    = ($current_page - 2 >= 0)                 ? $current_page - 2 : 0;
	$end      = ($start + 5 < ceil($count / $per_page))  ? $start + 5        : ceil($count / $per_page);
	$previous = ($current_page - 1 >= 0)                 ? $current_page - 1 : NULL;
	$next     = ($current_page + 1 < $end)               ? $current_page + 1 : NULL;
	?>

	<?php if( ! is_null($previous)): ?>
		<a class="paging paging-prev" href="<?= $uri.'?'.http_build_query( array( 'page' => $previous ) + $pagination_parameters ) ?>">previous</a>
		<span class="separator">|</span>
	<?php endif; ?>

	<?php for ($i = $start; $i < $pages && $i < $end ; ++$i): ?>
		<?php if ($i != $start): ?>
			<span class="separator">|</span>
		<?php endif; ?>
        <button class="<?= ($current_page == $i ? 'active ' : '') ?>paging" name="page" value="<?= $i ?>"><?= ($i + 1) ?></button>
	<?php endfor; ?>

	<?php if($next): ?>
		<span class="separator">|</span>
		<a class="paging paging-next" href="<?= $uri.'?'.http_build_query( array( 'page' => $next ) + $pagination_parameters ) ?>">next</a>
	<?php endif; ?>

	<?php if ($pages > 0): ?>
		<p>Showing <?= $current_page * $per_page + 1 ?> &ndash; <?= (($current_page + 1) * $per_page > $count) ? $count : (($current_page + 1) * $per_page) ?> of <?= $count ?> entries</p>
	<?php endif; ?>
</form>

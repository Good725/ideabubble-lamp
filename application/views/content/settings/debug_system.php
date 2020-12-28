<h1>System Debug Vairables</h1>

<h2>Session Values</h2>
<table class="table">

	<?php foreach ($_SESSION as $key => $value) { ?>
	<tr>
		<th><?php echo HTML::chars($key) ?></th>
		<td>
			<?php
			if (is_array($value))
			{
				IbHelpers::pre_r($value);
			}
			else{
				echo $value;
			}
			?>
		</td>
	</tr>
	<? } ?>

</table>

<h2>Cookie Values</h2>
<table class="table">

	<?php foreach ($_COOKIE as $key => $value) { ?>
	<tr>
		<th><?php echo HTML::chars($key) ?></th>
		<td>
			<?php
			if (is_array($value))
			{
				IbHelpers::pre_r($value);
			}
			else{
				echo $value;
			}
			?>
		</td>
	</tr>
	<? } ?>

</table>

<h2>Server Values</h2>
<table class="table">

	<?php foreach ($_SERVER as $key => $value) { ?>
	<tr>
		<th><?php echo HTML::chars($key) ?></th>
		<td>
			<?php
			if (is_array($value))
			{
				IbHelpers::pre_r($value);
			}
			else{
				echo $value;
			}
			?>
		</td>
	</tr>
	<? } ?>

</table>
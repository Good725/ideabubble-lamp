<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?php
$frequency = array(0 => array('*'), 1 => array('*'), 2 => array(0, 15), 3 => array('*'), array('*'));
?>
<tr>
	<th valign="top">Frequency</th>
	<td>
		<select name="frequency[minute]" multiple="multiple" size="5">
			<option value="*" <?=in_array('*', $frequency[0]) ? 'selected="selected"' : ''?>>Every minute</option>
			<?php for($i = 0 ; $i < 60 ; ++$i){ ?>
			<option value="<?=$i?>" <?=in_array($i, $frequency[0]) ? 'selected="selected"' : ''?>><?=$i?></option>
			<?php } ?>
		</select>
		<select name="frequency[hour]" multiple="multiple" size="5">
			<option value="*" <?=in_array('*', $frequency[1]) ? 'selected="selected"' : ''?>>Every hour</option>
			<?php for($i = 0 ; $i < 60 ; ++$i){ ?>
			<option value="<?=$i?>" <?=in_array($i, $frequency[1]) ? 'selected="selected"' : ''?>><?=$i?></option>
			<?php } ?>
		</select>
		<select name="frequency[day_of_month]" multiple="multiple" size="5">
			<option value="*" <?=in_array('*', $frequency[2]) ? 'selected="selected"' : ''?>>Every day of month</option>
			<option value="last" <?=in_array('last', $frequency[2]) ? 'selected="selected"' : ''?>>Last day of month</option>
			<?php for($i = 0 ; $i < 32 ; ++$i){ ?>
			<option value="<?=$i?>" <?=in_array($i, $frequency[2]) ? 'selected="selected"' : ''?>><?=$i?></option>
			<?php } ?>
		</select>
		<select name="frequency[day_of_week]" multiple="multiple" size="5">
			<option value="*" <?=in_array('*', $frequency[3]) ? 'selected="selected"' : ''?>>Every day of week</option>
			<?php foreach(array('0' => 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday') as $i => $day_of_week){ ?>
			<option value="<?=$i?>" <?=in_array($i, $frequency[3]) ? 'selected="selected"' : ''?>><?=$day_of_week?></option>
			<?php } ?>
		</select>
		<select name="frequency[month]" multiple="multiple" size="5">
			<option value="*" <?=in_array('*', $frequency[4]) ? 'selected="selected"' : ''?>>Every month</option>
			<?php foreach(array(1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December') as $i => $month){ ?>
			<option value="<?=$i?>" <?=in_array($i, $frequency[4]) ? 'selected="selected"' : ''?>><?=$month?></option>
			<?php } ?>
		</select>
	</td>
</tr>
</body>
</html>
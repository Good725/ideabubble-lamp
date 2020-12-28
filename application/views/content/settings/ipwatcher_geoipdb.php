<p><b>GeoIp Data File:</b> <?=$geoip_db_status['file']?><br />
<b>Last Update:</b> <?= $geoip_db_status['updated'] ? date('Y-m-d H:i:s', $geoip_db_status['updated']) : 'needs update'?><br />
<a href="/admin/settings/ipwatcher_geoipdb?update=1">Update Db</a>
</p>
<form method="get" class="form-horizontal">
	<div class="form-group">
		<label class="control-label col-sm-1">IP:</label>
		<div class="col-sm-2"><input class="form-control"  type="text" name="ip" value="<?=@$ip?>" /></div>
		<div class="col-sm-1"><button type="submit" class="add btn">Show</button></div>
	</div>
<?php
if(@$geocity){
	echo '<br />' . $geocity;
}
?>
</form>

<?= (isset($alert)) ? $alert : '' ?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<div id="messaging_signature" style="padding-top: 10px;">
	<form class="form-horizontal" name="messaging_signature_form" method="post">
		<div id="message-template-alerts">
			<?php if($save_result === false): ?>
				<div class="alert alert-danger"><strong>Error</strong> Signature was not saved <a href="#" class="close"></a></div>
			<?php elseif ($save_result === true): ?>
				<div class="alert alert-success"><strong>Success</strong> Signature has been saved <a href="#" class="close"></a></div>
			<?php endif; ?>
		</div>

		<div class="form-group">
			<label class="sr-only" for="signature-title">title</label>
			<div class="col-sm-10">
				<input class="form-control" id="signature-title" type="text" name="title" placeholder="Title" value="<?=html::chars(@$signature['title'])?>"/>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label">Format</label>
			<div class="col-sm-4">
				<select class="form-control" name="format">
					<?=html::optionsFromArray(array('HTML' => 'HTML', 'TEXT' => 'Text'), @$signature['format'])?>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label" for="signature-content">Content</label>
			<div class="col-sm-8">
				<textarea class="form-control" rows="8" id="signature-content" name="content"><?=html::entities(@$signature['content'])?></textarea>
			</div>
		</div>

		<button class="btn btn-primary" type="submit" name="action" value="save">Save</button>
		<button class="btn btn-default" type="submit" name="action" value="save_and_exit">Save &amp; Exit</button>
		<button class="btn btn-red" type="submit" name="action" value="delete">Delete</button>
		<a href="/admin/messaging/signatures" class="btn btn-default">Cancel</a>
		<button type="reset" name="reset" class="btn btn-default">Reset</button>
	</form>
</div>

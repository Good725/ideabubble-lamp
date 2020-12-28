<?=(isset($alert)) ? $alert : ''?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
            <div class="tab-pane" id="">
                <form class="col-sm-9 form-horizontal" id="form_new_notification" name="form_new_notification" action="/admin/notifications/save_notification" method="post">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="name">Notification ID</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control validate[required,ajax[ajaxNameCall]]" id="name" name="name" value="" placeholder="Enter the system name for this notification... e.g. contact-form">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="from">From</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control validate[required]" id="from" name="from" value="" placeholder="The from address that will be shown on emails received">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="subject">Subject</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control validate[required]" id="subject" name="subject" value="" placeholder="The subject of the email">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="description">Notification description</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" name="description" id="description"></textarea>
                        </div>
                    </div>

                    <div class="form-actions form-action-group">
                        <button type="submit" class="btn btn-primary">Save</button>
						<button type="reset" class="btn">Reset</button>
						<a href="/admin/notifications" class="btn">Cancel</a>
                    </div>
                </form>
            </div>

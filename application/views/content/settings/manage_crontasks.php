<script>
window.availableCronActions = <?=json_encode($availableActions);?>;
</script>
<form action="/admin/settings/save_crontask" method="post" id="manage_cron">
    <input type="hidden" name="id" id="id" value="<?=$crontask->get_id();?>"/>
    <input type="hidden" name="frequency" id="frequency" value="<?=htmlspecialchars($crontask->get_frequency());?>"/>
    <div class="tabbable form-horizontal col-sm-12">

		<div class="form-group">
			<div class="col-sm-9" id="page_edit_name">
				<input id="title" type="text" name="title" value="<?=$crontask->get_title();?>" class="form-control" />
			</div>
		</div>
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a href="#config" data-toggle="tab">Config</a></li>
            <li><a href="#activity" data-toggle="tab">Activity</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="config">
                <div class="form-group">
                    <div class="col-sm-2">Plugins</div>
                    <div class="col-sm-3">
                        <select class="form-control" name="plugin_id" id="plugin_id">
                            <option value="">Select Plugin</option>
                            <?php
                            $selectedPluginName = '';
                            foreach (Model_Plugin::get_all() as $key=>$plugin) {
                                if (isset($availableActions[$plugin['name']])) {
                                    if ($crontask->get_plugin_id() == $plugin['id']) {
                                        $selectedPluginName = $plugin['name'];
                                    }
                                ?>
                                <option
                                    value="<?= $plugin['id']; ?>" <?= ($crontask->get_plugin_id() == $plugin['id']) ? 'selected' : ''; ?>
                                    data-plugin="<?= $plugin['name'] ?>"><?= $plugin['friendly_name']; ?></option>
                                <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <select class="form-control" name="controller_action" id="controller_action">
                            <option value="">Select Action</option>
                            <?php
                            if (isset($availableActions[$selectedPluginName]))
                            foreach($availableActions[$selectedPluginName] as $controllerAction):
                            ?>
                            <option value="<?=$controllerAction;?>" <?=$crontask->get_action() == $controllerAction ? ' selected="selected"'  : ''?>><?=$controllerAction?></option>
                            <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <input class="form-control" type="text" placeholder="Extra Parameters" name="extra_parameters" value="<?=html::chars($crontask->get_extra_parameters())?>" />
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-2">Plugins</div>
                    <div class="col-sm-4">
                        <a href="#" id="plugin_settings">view plugin settings</a>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-2">Frequency</div>
                    <div class="col-sm-10">
                        <select multiple name="hour[]" id="hour">
                            <option value="*">Every Hour</option>
                            <?php
                            for($i = 0;$i < 24;$i++):
                                ?>
                                <option value="<?=$i;?>"><?=$i;?></option>
                            <?php
                            endfor;
                            ?>
                        </select>

                        <select multiple name="minute[]" id="minute">
                            <option value="*">Every Minute</option>
                            <?php
                            for($i = 0;$i < 60;$i++):
                                ?>
                                <option value="<?=$i;?>"><?=$i;?></option>
                            <?php
                            endfor;
                            ?>
                        </select>
                        <select multiple name="month[]" id="month">
                            <option value="*">Every Month</option>
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                        <select multiple name="day_of_month[]" id="day_of_month">
                            <option value="*">Every Day of Month</option>
                            <?php
                            for($i = 1;$i < 32;$i++):
                                ?>
                                <option value="<?=$i;?>"><?=$i;?></option>
                            <?php
                            endfor;
                            ?>
                        </select>
                        <select multiple name="day_of_week[]" id="day_of_week">
                            <option value="*">Every Day</option>
                            <option value="1">Monday</option>
                            <option value="2">Tuesday</option>
                            <option value="3">Wednesday</option>
                            <option value="4">Thursday</option>
                            <option value="5">Friday</option>
                            <option value="6">Saturday</option>
                            <option value="7">Sunday</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-2">Publish</div>
                    <div class="col-sm-8">
                        <div class="btn-group" data-toggle-name="publish" data-toggle="buttons">
							<label class="btn<?= ($crontask->get_publish() == 1) ? ' active' : '' ?>">
								<input type="radio"<?= ($crontask->get_publish() == 1) ? ' checked="checked"' : '' ?> value="1" name="publish">Yes
							</label>
							<label class="btn<?= ( $crontask->get_publish() != 1) ? ' active' : '' ?>">
								<input type="radio"<?= ($crontask->get_publish() != 1) ? ' checked="checked"' : '' ?> value="0" name="publish">No
							</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-2">Send Email On Complete</div>
                    <div class="col-sm-8">
                        <div class="btn-group" data-toggle-name="publish" data-toggle="buttons">
                            <label class="btn<?= ($crontask->get_send_email_on_complete() == 1) ? ' active' : '' ?>">
                                <input type="radio"<?= ($crontask->get_send_email_on_complete() == 1) ? ' checked="checked"' : '' ?> value="1" name="send_email_on_complete">Yes
                            </label>
                            <label class="btn<?= ( $crontask->get_send_email_on_complete() != 1) ? ' active' : '' ?>">
                                <input type="radio"<?= ($crontask->get_send_email_on_complete() != 1) ? ' checked="checked"' : '' ?> value="0" name="send_email_on_complete">No
                            </label>
                        </div>
                    </div>
                </div>

            </div>
            <div class="tab-pane" id="activity">
				<table class="table">
					<tr><th>Started</th><th>Finished</th><th>Output</th></tr>
					<?php foreach($logs as $i => $log){ ?>
					<tr><td><?=$log['started']?></td><td><?=$log['finished']?></td><td><pre><?=htmlentities($log['output'])?></pre></td></tr>
					<?php } ?>
				</table>
			</div>
        </div>

        <div class="fixedMenu well row">
            <button type="button" class="btn btn-primary" id="btn_save">Save</button>
            <button type="button" class="btn btn-primary" id="btn_save_exit">Save &amp; Exit</button>
			<?php if($crontask->get_id()){ ?>
			<button type="button" class="btn btn-primary" id="btn_run">Run</button>
            <button type="button" class="btn btn-danger"  id="btn_delete">Delete</button>
			<?php } ?>
            <button type="reset" class="btn" id="btn_reset">Reset</button>
            <a href="/admin/settings/crontasks" class="btn">Cancel</a>
        </div>

    </div>
</form>

<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Settings</h3>
			</div>

			<div class="modal-body">
				<form id="modal_content" action="/admin/settings/" method="post">

				</form>
			</div>

			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-primary" id="update_settings" data-dismiss="modal" aria-hidden="true">Save changes</button>
			</div>

		</div>
	</div>
</div>
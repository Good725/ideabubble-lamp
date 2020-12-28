<?= (isset($alert)) ? $alert : ''; ?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<div id="calendar_header_buttons" class="pull-right">
    <span id="calendar_add_event" style="display: none;"><button class="btn btn-primary" type="button">Add Event</button></span>
    <span id="calendar_add_type" style="display: none;"><a href="/admin/calendars/edit_type" class="btn btn-primary">Add Type</a></span>
    <span id="calendar_add_rule" style="display: none;"><a href="/admin/calendars/edit_rule" class="btn btn-primary">Add Rule</a></span>
</div>

<div id="calendar_views" class="tabbable">
    <ul id="daily_frequency" class="nav nav-tabs">
        <li id="calendar_tab" class="active"><a href="#calendar" data-toggle="tab">Calendar</a></li>
        <li id="event_tab"><a href="#calendar_event" data-toggle="tab">Events</a></li>
        <li id="type_tab"><a href="#calendar_type" data-toggle="tab">Types</a></li>
        <li id="rule_tab"><a href="#calendar_rule" data-toggle="tab">Rules</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="calendar">
            <div id="datepicker-outer" class="form-group">
                <label class="col-sm-2 control-label" for="datepicker">View Your Calendar</label>
                <div class="col-sm-6">
                    <div id="main-calendar"></div>
                </div>
            </div>
        </div>
		<style>
			.eventCalendar-list-wrap {
				display: none;
			}
			.eventCalendar-wrap {
				border: none;
				box-shadow: none;
			}
		</style>
        <? // Event View ?>
        <div class="tab-pane" id="calendar_event">
            <div id="event_list">
                <table id="calendar_events" class='table table-striped dataTable'>
                    <thead>
						<tr>
							<th>Event ID</th>
							<th>Event Title</th>
							<th>Event Type</th>
							<th>Event Rule</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th>Publish</th>
							<th>Delete</th>
							<th>Updated</th>
						</tr>
                    </thead>
                    <tbody>
						<?php foreach($events as $event): ?>
							<tr id="event<?= $event->id; ?>" data-item="event" data-row_id="<?= $event->id; ?>">
								<td><a href="/admin/calendars/edit_event2/<?= $event->id ?>"><?= $event->id; ?></a></td>
								<td><a href="/admin/calendars/edit_event2/<?= $event->id ?>"><?= $event->title; ?></a></td>
								<td><a href="/admin/calendars/edit_event2/<?= $event->id ?>"><?= $event->type->title; ?></a></td>
								<td><a href="/admin/calendars/edit_event2/<?= $event->id ?>"><?= $event->rule->title; ?></a></td>
								<td><a href="/admin/calendars/edit_event2/<?= $event->id ?>"><?= date('d-m-Y',strtotime($event->start_date)); ?></a></td>
								<td><a href="/admin/calendars/edit_event2/<?= $event->id ?>"><?= date('d-m-Y',strtotime($event->end_date)); ?></a></td>
								<td class="publish" data-publish="<?= $event->publish; ?>">
									<span class="hidden"><?= $event->publish ?></span>
									<span class="icon-<?= $event->publish == 1 ? 'ok' : 'ban-circle'; ?>"></span>
								</td>
								<td class="delete" data-delete="<?= $event->deleted; ?>">
									<span class="icon-<?= $event->deleted == 1 ? 'ok' : 'ban-circle'; ?>"></span>
								</td>
								<td><a href="/admin/calendars/edit_event2/<?= $event->id ?>"><?php echo $event->updated_on; ?></a></td>
							</tr>
						<?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div id="event_form">

            </div>
        </div>

        <? // List Types ?>
        <div class="tab-pane" id="calendar_type">
            <div id="type_list">
                <table id="calendar_types" class='table table-striped dataTable'>
                    <thead>
						<tr>
							<th>Type ID</th>
							<th>Type Title</th>
							<th>Publish</th>
							<th>Delete</th>
							<th>Updated</th>
						</tr>
                    </thead>
                    <tbody>
						<?php foreach($types as $type): ?>
							<tr id="type<?= $type->id; ?>" data-item="type"  data-row_id="<?php echo $type->id; ?>">
								<td><a href="<?php echo URL::Site('admin/calendars/edit_type/' . $type->id); ?>"><?php echo $type->id; ?></a></td>
								<td><a href="<?php echo URL::Site('admin/calendars/edit_type/' . $type->id); ?>"><?php echo $type->title; ?></a></td>
								<td class="publish" data-publish="<?= $type->publish; ?>"><i class="icon-<?php echo $type->publish == 1 ? 'ok' : 'ban-circle'; ?>"></td>
								<td class="delete" data-delete="<?= $type->deleted; ?>"><i class="icon-<?php echo $type->deleted == 1 ? 'ok' : 'ban-circle'; ?>"></td>
								<td><a href="<?php echo URL::Site('admin/calendars/edit_type/' . $type->id); ?>"><?php echo $type->updated_on; ?></a></td>
							</tr>
						<?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div id="type_form">

            </div>
        </div>

        <? // Rules View ?>
        <div class="tab-pane" id="calendar_rule">
            <div id="rule_list">
                <table id="calendar_rules" class='table table-striped dataTable'>
                    <thead>
                    <tr>
                        <th>Rule ID</th>
                        <th>Rule Title</th>
                        <th>Plugin</th>
                        <th>Description</th>
                        <th>Publish</th>
                        <th>Delete</th>
                        <th>Updated</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($rules as $rule): ?>
                        <tr id="rule<?= $rule->id; ?>" data-item="rule"  data-row_id="<?php echo $rule->id; ?>">
                            <td><a href="<?php echo URL::Site('admin/calendars/edit_rule/' . $rule->id); ?>"><?php echo $rule->id; ?></a></td>
                            <td><a href="<?php echo URL::Site('admin/calendars/edit_rule/' . $rule->id); ?>"><?php echo $rule->title; ?></a></td>
                            <td><a href="<?php echo URL::Site('admin/calendars/edit_rule/' . $rule->id); ?>"><?php echo $rule->plugin_name; ?></a></td>
                            <td><a href="<?php echo URL::Site('admin/calendars/edit_rule/' . $rule->id); ?>"><?php echo $rule->description; ?></a></td>
                            <td class="publish" data-publish="<?= $rule->publish;?>"><i class="icon-<?php echo $rule->publish == 1 ? 'ok' : 'ban-circle'; ?>"></td>
                            <td class="delete" data-delete="<?= $rule->deleted;?>"><i class="icon-<?php echo $rule->deleted == 1 ? 'ok' : 'ban-circle'; ?>"></td>
                            <td><a href="<?php echo URL::Site('admin/calendars/edit_rule/' . $rule->id); ?>"><?php echo $rule->updated_on; ?></a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div id="rule_form">

            </div>
        </div>
    </div>
</div>

<? // Add Event Modal ?>
<div id="add_event_modal" class="modal fade">
    <div class="modal-dialog">

        <form id="calendar_event" class="form-horizontal" action="<?php echo URL::Site('admin/calendars/save_event2/') ?>" method="post">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3>Add an Event</h3>
            </div>
        <div class="modal-body form-horizontal">
            <fieldset>
                <input type="hidden" id="id" name="id" value="new">
                <div class="form-group">
                    <label for="title" class="col-sm-2 control-label">Title</label>

                    <div class="col-sm-7">
                        <input type="text" id="title" name="title" class="form-control popinit" rel="popover" value="" placeholder="Enter Event Title" required="required">
                    </div>
                </div>

                <? // Select Event Type ?>
                <div class="form-group">
                    <label for="type_id" class="col-sm-2 control-label">Event Type</label>

                    <div class="col-sm-7">
                        <select type="text" id="type_id" name="type_id" class="form-control popinit">
                            <option value="0">Please select event type</option>
                            <?php foreach ($publish_types as $type):?>
                                <option value="<?= $type->id;?>""><?= $type->title ;?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <? // Select Event rule ?>
                <div class="form-group">
                    <label for="rule_id" class="col-sm-2 control-label">Event Rule</label>

                    <div class="col-sm-7">
                        <select type="text" id="rule_id" name="rule_id" class="form-control popinit">
                            <option value="0">Please select event rule</option>
                            <?php foreach ($publish_rules as $rule):?>
                                <option value="<?= $rule->id;?>" "><?= $rule->title ;?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <? // Select Start Date ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="start_date">Start Date</label>
                    <div class="col-sm-2">
                        <input type="text"  class="form-control datepicker" id="start_date" name="start_date" value="" placeholder="Start Date" required="required"/>
                    </div>
                    <label class="col-sm-2 control-label" for="end_date">End Date</label>
                    <div class="col-sm-2">
                        <input type="text"  class="form-control datepicker" id="end_date" name="end_date" value="" placeholder="End Date"/>
                    </div>
                </div>

                <? // Publish ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="publish">Publish</label>

                    <div class="col-sm-4">
						<?= html::toggle_button('publish', __('Yes'), __('No'), TRUE) ?>
                    </div>
                </div>
            </fieldset>

            <input type="hidden" id="save_exit" name="save_exit" value="true" />
        </div>
            <div class="modal-footer">
                <div class="well">
                    <button type="submit" class="btn btn-primary">Add Event</button>
                    <button type="reset" class="btn" id="event-form-reset">Reset</button>
                    <button href="#" class="btn" data-dismiss="modal">Cancel</button>
                </div>
            </div>
    </form>
</div>

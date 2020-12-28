<div id="calendar_edit_event" class="col-sm-12">
    <form id="calendar_event_edit_form" class="form-horizontal" action="<?php echo URL::Site('admin/calendars/save_event2/') ?>" method="post">
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
        <fieldset>
            <div class="form-group">
                <label for="title" class="col-sm-2 control-label">Title</label>

                <div class="col-sm-7">
                    <input type="text" id="title" name="title" class="form-control popinit" rel="popover" value="<?= $event->title ?>" placeholder="Enter Event Title" required="required">
                </div>
            </div>
            <input type="hidden" id="id" name="id" value="<?= $event->id;?>">

            <? // Select Event Type ?>
            <div class="form-group">
                <label for="type_id" class="col-sm-2 control-label">Event Type</label>

                <div class="col-sm-7">
                    <select type="text" id="type_id" name="type_id" class="form-control popinit">
                        <option value="0">Please select event type</option>
                        <?php foreach ($types as $type):
                            $selected = $event->type_id == $type->id ? 'selected="selected"' : '' ;
                            ?>
                            <option value="<?= $type->id;?>" <?= $selected;?>"><?= $type->title ;?></option>
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
                        <?php foreach ($rules as $rule):
                            $selected = $event->rule_id == $rule->id ? 'selected="selected"' : '' ;
                            ?>
                            <option value="<?= $rule->id;?>" <?= $selected;?>"><?= $rule->title ;?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <? // Select Start Date ?>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="start_date">Start Date</label>
                <div class="col-sm-2">
                    <input type="text"  class="form-control datepicker" id="start_date" name="start_date" value="<?= date('d-m-Y',strtotime($event->start_date)); ?>" required="required"/>
                </div>
                <label class="col-sm-2 control-label" for="end_date">End Date</label>
                <div class="col-sm-2">
                    <input type="text"  class="form-control datepicker" id="end_date" name="end_date" value="<?= date('d-m-Y',strtotime($event->end_date)); ?>"/>
                </div>
            </div>

            <? // Publish ?>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="publish">Publish</label>

                <div class="col-sm-4">
                    <div class="btn-group" data-toggle="buttons">
                        <?php $publish = ($event->publish == '1' OR is_null($event->publish) ); ?>
                        <label class="btn btn-plain<?= $publish ? ' active' : '' ?>">
                            <input type="radio" name="publish" value="1" id="publish_yes"<?= $publish ? ' checked' : '' ?> />Yes
                        </label>
                        <label class="btn btn-plain<?= ( ! $publish) ? ' active' : '' ?>">
                            <input type="radio" name="publish" value="0" id="publish_no"<?= ( ! $publish) ? ' checked' : '' ?> />No
                        </label>
                    </div>
                </div>
            </div>
        </fieldset>

        <? // Action Buttons ?>
        <input type="hidden" id="save_exit" name="save_exit" value="false" />

        <div class="col-sm-12">
            <div class="well">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="submit" class="btn btn-primary" onclick="$('#save_exit')[0].setAttribute('value', 'true');">Save &amp; Exit</button>
                <button type="reset" class="btn" id="event-form-reset">Reset</button>
                <?php if (is_numeric($event->id)) : ?>
                    <a class="btn btn-danger" id="btn_delete" data-item="calendar_events" data-id="<?=$event->id ?>">Delete</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

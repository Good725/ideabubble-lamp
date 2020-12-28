<?php
$courses = DB::query(Database::SELECT, 'SELECT
		DISTINCT
		c.id,
		c.title as `course`

	FROM plugin_courses_schedules s
		INNER JOIN plugin_courses_courses c ON s.course_id = c.id
		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id
	WHERE
		s.`delete` = 0 AND c.deleted = 0 AND e.`delete` = 0 AND
		e.datetime_start >= NOW()
	ORDER BY c.title')->execute()->as_array();

$schedules = DB::query(Database::SELECT, '(SELECT
		DISTINCT
		s.id,
		CONCAT_WS(\' \', s.`name`, \' \', pl.`name`, l.name) AS `schedule`
	FROM plugin_courses_schedules s
		INNER JOIN plugin_courses_courses c ON s.course_id = c.id
		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id
		LEFT JOIN plugin_courses_locations l ON s.location_id = l.id
		LEFT JOIN plugin_courses_locations pl ON l.parent_id = pl.id
	WHERE
		s.`delete` = 0 AND c.deleted = 0 AND e.`delete` = 0 AND
		e.datetime_start >= NOW()
	ORDER BY s.`name`)')->execute()->as_array();

$trainers = DB::query(Database::SELECT, '(SELECT
		DISTINCT t.id AS `id`,
		CONCAT_WS(\' \', t.title, t.first_name, t.last_name) AS `trainer`
	FROM plugin_courses_schedules s
		INNER JOIN plugin_courses_courses c ON s.course_id = c.id
		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id
		INNER JOIN plugin_contacts3_contacts t ON e.trainer_id = t.id
	WHERE
		s.`delete` = 0 AND c.deleted = 0 AND e.`delete` = 0 AND
		e.datetime_start >= NOW()
ORDER BY `trainer`)')->execute()->as_array();

$logged_in_user = Auth::instance()->get_user();
?>

<div class="textalert-area" id="right-panel-text-alert">

	<form class="form-horizontal textalert-area-expanded hidden" id="form_text_alert" name="form_text_Alert" action="/admin/bookings/text_alert/" method="post">

		<div class="textalert-header">
			<span class="textalert-header-icon">
				<span class="icon-bullhorn"></span>
			</span>

			<span class="textalert-user">
				<?= $logged_in_user['name'].' '.$logged_in_user['surname'] ?>

				<img class="textalert-user-avatar" src="<?= URL::get_avatar($logged_in_user['id']); ?>" alt="profile" width="50" height="50" title="<?= __('Profile: ').$logged_in_user['name'] ?>" />
			</span>

			<button type="button" class="textalert-toggle">
				<span class="icon-times"></span>
			</button>
		</div>

		<div class="textalert-body">

			<h3><?= __('Alert' ) ?></h3>

			<div class="form-group">
				<div class="col-sm-12">
					<label class="sr-only" for="textalert-message"><?= __('Message') ?></label>
					<button type="button" class="popinit alertarea-info_icon" data-content="<?= __('You can use $course, $schedule, $trainer, $datetime variables in your message') ?>" rel="popover" data-trigger="focus hover" data-placement="left" data-original-title="<?= __('Hint') ?>">
						<span class="icon-info"></span>
					</button>
					<textarea class="form-control" id="textalert-message" name="message" rows="3" placeholder="<?= __('Text of Alert') ?>"></textarea>
				</div>
			</div>

			<div class="form-group">
				<label class="sr-only" for="textalert-course"><?= __('Course') ?></label>

				<div class="col-sm-12">
					<div class="select">
						<select class="form-control" id="textalert-course" name="course_id">
							<option value=""><?= __('Choose Course') ?></option>
							<?php foreach ($courses as $course): ?>
								<option value="<?=$course['id']?>"><?=$course['course']?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="sr-only" for="textalert-class"><?= __('Class') ?></label>

				<div class="col-sm-12">
					<div class="select">
						<select class="form-control" id="textalert-class" name="schedule_id">
							<option value=""><?= __('Choose Class') ?></option>
							<?php foreach ($schedules as $schedule): ?>
								<option value="<?=$schedule['id']?>"><?=$schedule['schedule']?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="sr-only" for="textalert-teacher"><?= __('Teacher') ?></label>

				<div class="col-sm-12">
					<div class="select">
						<select class="form-control" id="textalert-teacher" name="trainer_id">
							<option value=""><?= __('Choose Teacher') ?></option>
							<?php foreach ($trainers as $trainer): ?>
								<option value="<?=$trainer['id']?>"><?=$trainer['trainer']?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</div>

			<hr />

			<h3><?= __('Date') ?></h3>

			<div class="form-group">
				<label class="sr-only" for="textalert-interval"><?= __('Period Type') ?></label>

				<div class="col-sm-12">
					<div class="select">
						<select class="form-control" id="textalert-interval" name="interval">
							<option value="MINUTE" selected="selected">Minute</option>
							<option value="HOUR">Hour</option>
							<option value="DAY">Day</option>
						</select>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="sr-only" for="textalert-interval_amount"><?= __('Period') ?></label>

				<div class="col-sm-12">
					<input class="form-control" id="textalert-interval_amount" type="text" name="interval_amount" placeholder="" value="10">
				</div>
			</div>

			<hr />

			<h3><?= __('Send Alarm') ?></h3>

			<div class="textalert-alarm_type">
				<label>
					<input type="checkbox" name="type[sms]" value="sms" />
					SMS
				</label>
			</div>

			<div class="textalert-alarm_type">
				<label>
					<input type="checkbox" name="type[email]" value="email" />
					Email
				</label>
			</div>
		</div>

		<div class="textalert-footer">
			<div class="form-action-group">
				<button class="btn btn-primary" type="button" name="send"><?= __('Send') ?></button>
			</div>
		</div>
	</form>

	<div class="textalert-area-collapsed" id="textalert-area-collapsed">
		<button type="button" class="textalert-toggle">
			<span class="icon-bullhorn"></span>
		</button>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="textalert-response-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <p id="textalert-response-message"></p>
            </div>

            <div class="modal-footer form-actions">
                <button type="button" class="btn" data-dismiss="modal" id="textalert-response-button">OK</button>
            </div>
        </div>
    </div>
</div>
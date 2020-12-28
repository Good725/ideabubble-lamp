<?php foreach ($survey->has_questions->find_all_published() as $has_question): ?>
	<?php
	$question = $has_question->question;
	$type = $question->answer->type->stub;
	?>
	<div class="set-sequence-item">
		<div class="form-group set-sequence-item-question">
			<div class="col-sm-12">
				<strong>Q<?= 1 + $has_question->order_id ?>: <?= $question->title ?></strong>
			</div>
		</div>

		<div class="set-sequence-item-options">
			<?php $options = $question->answer->options->find_all_published(); ?>

			<?php if (count($options) > 0): ?>
				<?php foreach ($question->answer->options->find_all_published() as $option): ?>

					<?php $sequence_item = $sequence->items->where('answer_option_id', '=', $option->id)->where('question_id', '=', $question->id)->find(); ?>

					<div class="form-group set-sequence-item-option">
						<div class="col-sm-offset-1 col-sm-2"><?= ($type == 'radio') ? $option->label : '' ?></div>

						<div class="col-sm-4">
							<label class="sr-only">Action</label>
							<select class="form-control answer_action">
								<option value="">-- Select action --</option>
								<option value="0"<?= ($sequence_item->survey_action === '0') ? ' selected' : '' ?>>Go to question #</option>
								<option value="1"<?= ($sequence_item->survey_action === '1') ? ' selected' : '' ?>>End questionnaire</option>
							</select>
						</div>

						<div class="col-sm-5">
							<label class="sr-only">Target question</label>
							<select class="form-control answer_target_question">
								<?php $target_questions = $survey->has_questions->where('question_id', '!=', $question->id)->find_all_published(); ?>

								<option value="">-- Select target --</option>
								<?php foreach ($target_questions as $target): ?>
									<?php $selected = ($sequence_item->target_id == $target->question->id) ? ' selected' : '' ?>
									<option value="<?= $target->question->id ?>"<?= $selected ?>><?= $target->question->title ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="sequence_answers"
							 data-id="<?= $sequence_item->id ?>"
							 data-question_id="<?= $question->id ?>"
							 data-answer_id="<?= $option->id ?>"
							 data-survey_action="<?= $sequence_item->survey_action ?>"
							 data-target_id="<?= $sequence_item->target_id ?>">
						</div>

					</div>
				<?php endforeach; ?>
			<?php else: ?>
				<?php $sequence_item = $sequence->items->where('question_id', '=', $question->id)->find(); ?>

				<div class="form-group">
					<div class="col-sm-offset-1 col-sm-2"></div>

					<div class="col-sm-4">
						<label class="sr-only">Action</label>
						<select class="form-control answer_action">
							<option value="">-- Select action --</option>
							<option value="0"<?= ($sequence_item->survey_action === '0') ? ' selected' : '' ?>>Go to question #</option>
							<option value="1"<?= ($sequence_item->survey_action === '1') ? ' selected' : '' ?>>End questionnaire</option>
						</select>
					</div>

					<div class="col-sm-5">
						<label class="sr-only">Target question</label>
						<select class="form-control answer_target_question">
							<?php $target_questions = $survey->has_questions->where('question_id', '!=', $question->id)->find_all_published(); ?>

							<option value="">-- Select target --</option>
							<?php foreach ($target_questions as $target): ?>
								<?php $selected = ($sequence_item->target_id == $target->question->id) ? ' selected' : '' ?>
								<option value="<?= $target->question->id ?>"<?= $selected ?>><?= $target->question->title ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="sequence_answers"
						 data-id="<?= $sequence_item->id ?>"
						 data-question_id="<?= $question->id ?>"
						 data-survey_action="<?= $sequence_item->survey_action ?>"
						 data-target_id="<?= $sequence_item->target_id ?>"
						 data-answer_id="">
					</div>

				</div>
			<?php endif; ?>
		</div>
	</div>
<?php endforeach; ?>

<?php foreach ($questions as $question): ?>
    <div
        class="survey_question_group-question clearfix"
        data-report_id="<?= $report->get_id() ?>"
        data-question_id="<?= $question['id'] ?>"
        data-total="<?= $question['total_responses'] ?>"
        <?php if (!empty($question['highchart_data'])): ?>
            data-highchart_data="<?= htmlentities(json_encode($question['highchart_data'])) ?>"
        <?php endif; ?>
        >
        <div class="survey_question_group-question_text">
            <p>Q<?= (int) $question['order_id'] + 1 ?>. <?= $question['title'] ?></p>
        </div>

        <?php if (in_array($question['answer_type'], array('checkbox', 'radio', 'select'))): ?>
            <figure>
                <div class="survey_question_group-bar_chart" id="survey-question-group-<?= (int) $question['order_id'] + 1 ?>"
                     style="width:170px;width:calc(20px + 50px * <?= count($question['answers_data']) ?>);height:170px;"></div>
                <figcaption>Total: <?= $question['total_responses'] ?></figcaption>
            </figure>
        <?php elseif (in_array($question['answer_type'], array('input', 'textarea'))): ?>
            <div>
                <button
                    type="button" class="btn btn-default"
                    data-toggle="modal" data-target="#survey_question_group-<?= $report->get_id() ?>-answers_modal"
                    >View Responses</button>
                <div class="survey_question_group-view_responses hidden">
                    <?php foreach ($question['answers_data'] as $answer_data): ?>
                        <?php if (trim($answer_data['answer'])): ?>
                            <blockquote>
                                <?php
                                // Escape special characters, render line breaks as p or br
                                echo $answer = '<p>'.preg_replace('/\n/', '<br />', preg_replace('/\n(\s*\n)+/', '</p><p>', htmlentities(trim($answer_data['answer'])))).'</p>';
                                ?>
                            </blockquote>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<div class="modal fade survey_question_group-answers_modal" id="survey_question_group-<?= $report->get_id() ?>-answers_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title"><?= __('Responses') ?></h3>
            </div>

            <div class="modal-body"></div>

            <div class="modal-footer action-buttons text-center">
                <button type="button" class="btn btn-primary btn-lg" data-dismiss="modal" style="min-width: 6em;"><?= __('OK') ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    $('.survey_question_group-question[data-highchart_data][data-report_id="<?= $report->get_id() ?>"]').each(function() {
        var total          = $(this).data('total');
        var highchart_data = $(this).data('highchart_data');

        highchart_data.plotOptions.series.dataLabels.formatter = function() {
            var percentage = (total == 0) ? 0 : (this.y / total) * 100;
            return Math.round(percentage) + '%';
        };

        highchart_data.tooltip.formatter = function() {
            return this.x + ': ' + this.y;
        };

        new Highcharts.Chart(highchart_data);
    });
</script>
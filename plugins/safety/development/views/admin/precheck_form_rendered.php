<?php
$form = new IbForm('safety-precheck-form', '#', 'post', ['layout' => 'vertical']);
$form->published_field = false;
$columns = [null => 'Answer'];
$has_stock_selector = $survey->has_stock_selector && $survey->stock_category_id;
?>

<div class="precheck-form"
     data-survey_id="<?= $survey->id ?>"
     data-survey_result_id="<?= $result['result_id'] ?>"
     data-has_children="<?= $survey->has_children() ? 'true' : 'false' ?>"
>
    <h2><?= htmlspecialchars($survey->title) ?></h2>

    <?php if ($has_stock_selector): ?>
        <div class="row gutters">
            <div class="col-sm-6">
                <?php
                $columns = $survey->stock_category->inventory->order_by('title')->find_all_undeleted()->as_array('id', 'title');

                echo $form->multiselect(
                    $survey->stock_selector_text,
                    'stock_ids[]',
                    $columns,
                    array_keys($responses),
                    ['class' => 'precheck-select-items']
                );
                ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($survey->has_course_selector || $survey->has_schedule_selector): ?>
        <div class="row gutters">
            <div class="col-sm-6">
                <?php
                if ($survey->has_course_selector) {
                    $courses = ORM::factory('Course')->find_all_published()->as_array('id', 'title');
                    echo $form->combobox('Select a course', 'course_id', $courses, $result['course_id']);
                }
                ?>
            </div>

            <div class="col-sm-6">
                <?php
                if ($survey->has_schedule_selector) {
                    $schedules = ORM::factory('Course_Schedule')->find_all_published()->as_array('id', 'name');
                    echo $form->combobox('Select a schedule', 'schedule_id', $schedules, $result['schedule_id']);
                }
                ?>
            </div>
        </div>
    <?php endif; ?>

    <table class="table table-sticky precheck-questions-table<?= $has_stock_selector ? ' hidden' : '' ?>">
        <thead>
            <tr>
                <th scope="col" rowspan="2"><abbr title="Number">#</abbr></th>
                <th scope="col" rowspan="2">Question</th>
                <?php foreach ($columns as $stock_id => $column): ?>
                    <th scope="col" rowspan="2"
                        <?= count($columns) == 1 ? ' style="min-width: 11em;"' : '' ?>
                        <?= $stock_id ? ' class="hidden" data-stock_id="'.$stock_id.'"' : '' ?>
                    ><?= htmlspecialchars($column) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($survey->get_groups() as $group): ?>
                <?php if (!empty($group->title)): ?>
                    <tr>
                        <th></th>
                        <th scope="col"><?= htmlspecialchars($group->title) ?></th>
                        <th scope="col" colspan="<?= count($columns) ?>"></th>
                    </tr>
                <?php endif; ?>

                <?php $has_questions = $group->has_questions->where('survey_id', '=', $survey->id)->order_by('order_id')->find_all_published(); ?>

                <?php foreach ($has_questions as $has_question): ?>
                    <?php $question = $has_question->question->deleted ? new Model_Question : $has_question->question; ?>

                    <tr>
                        <td><?= ++$number ?></td>
                        <td class="w-100"><?= htmlspecialchars($question->title) ?></td>

                        <?php foreach ($columns as $stock_id => $column): ?>
                            <td<?= $stock_id ? ' class="hidden" data-stock_id="'.$stock_id.'"' : '' ?>>
                                <div>
                                    <?php
                                    $question_response = @$responses[$stock_id ? $stock_id : 0]['responses'][$question->id];
                                    $action_needed = ($question_response['value'] == 'no' && !$survey->has_children());

                                    echo $question->render([
                                        'name' => 'questions['.($stock_id ? $stock_id : 0).']['.$question->id.']',
                                        'value' => $question_response['value']
                                    ]);
                                    ?>

                                    <button
                                        type="button"
                                        class="precheck-view-corrective button--plain text-primary<?= $action_needed ? '' : ' hidden' ?>"
                                        >
                                        View corrective action
                                    </button>
                                </div>
                            </td>
                        <?php endforeach; ?>
                    </tr>

                    <?php foreach ($columns as $stock_id => $column): ?>
                        <?php
                        $question_response = @$responses[$stock_id ? $stock_id : 0]['responses'][$question->id];
                        $action_needed = ($question_response['value'] == 'no' && !$survey->has_children());
                        ?>

                        <tr class="precheck-todo<?= $action_needed ? '' : ' hidden' ?>"
                            data-question_id="<?= $question->id ?>"
                            data-stock_id="<?= $stock_id ?>"
                        >
                            <td></td>
                            <td colspan="<?= 2 + count($columns) ?>">
                                <button
                                    type="button"
                                    class="btn-link p-0 w-100"
                                    data-toggle="collapse" data-target=".precheck-todo[data-question=<?= $question->id ?>]" aria-expanded="false"
                                >
                                    <h4>
                                        Flag corrective action

                                        <?php if ($stock_id): ?>
                                            - <strong><?= htmlspecialchars($column) ?></strong>
                                        <?php endif; ?>

                                        <span class="expanded-invert icon-angle-down"></span>
                                    </h4>
                                </button>

                                <div class="precheck-todo collapse" data-question="<?= $question->id ?>">
                                    <?php
                                    $name_prefix = 'todos['.($stock_id ? $stock_id : 0).']['.$question->id.']';
                                    $todo = @$responses[$stock_id ? $stock_id : 0]['responses'][$question->id]['todo'];
                                    ?>

                                    <input type="hidden" name="<?= $name_prefix ?>[id]" value="<?= @$todo['id'] ?>" />

                                    <?php
                                    if (!empty($todo) && !empty($todo['summary'])) {
                                        $summary = $todo['summary'];
                                    } else {
                                        $summary = $stock_id
                                            ? $column . ' is failing the following criteria:' . "\n" . $question->title
                                            : 'The following criteria is failing:' . "\n" . $question->title;
                                    }

                                    echo $form->textarea(
                                            'Summary',
                                            $name_prefix.'[summary]',
                                            $summary,
                                            $action_needed ? [] : ['disabled' => 'disabled']
                                        );
                                    ?>

                                    <div class="row gutters mb3">
                                        <div class="col-sm-6">
                                            <?php
                                            $assignee = new Model_Contacts3_Contact(@$todo['assignee_id']);
                                            echo $form->ajax_typeselect(
                                                'Assignee', // label
                                                $name_prefix.'[assignee_id]', // name
                                                $assignee->id, // hidden value
                                                $assignee->id ? ($assignee->id.' - '.$assignee->get_full_name()) : '', // display value
                                                $action_needed ? [] : ['disabled' => 'disabled'], // hidden attributes
                                                $action_needed ? [] : ['disabled' => 'disabled'], // display attributes
                                                ['url' => '/admin/contacts3/find_contact'] // args
                                            );
                                            echo $form->select(
                                                'Status',
                                                $name_prefix.'[status_id]',
                                                ['Open', 'In progress', 'Done'],
                                                isset($todo['status']) ? $todo['status'] : '',
                                                $action_needed ? [] : ['disabled' => 'disabled']
                                            );
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

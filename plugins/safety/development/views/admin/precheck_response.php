<div class="safety-precheck-quiz-wrapper" data-type="<?= htmlspecialchars($type) ?>">
    <h2><?= htmlspecialchars($heading) ?></h2>

    <?php if (!empty($columns)): ?>
        <?php $has_custom_columns = true; ?>
        <div class="row gutters">
            <div class="col-sm-6">
                <?php
                $args = ['multiselect_options' => ['includeSelectAllOption' => true, 'selectAllText' => __('ALL')], 'please_select' => false];
                $attributes = ['multiple' => 'multiple', 'class' => 'precheck-select-items'];
                $label = isset($column_text) ? $column_text : 'Select '.$type;
                echo $form->select($label, 'columns_'.$type, $columns, null, $attributes, $args);
                ?>
            </div>
        </div>
    <?php else: ?>
        <?php
        $has_custom_columns = false;
        $columns = ['Answer'];
        ?>
    <?php endif; ?>

    <?php if ($type == 'locations'): ?>
        <div class="row gutters">
            <div class="col-sm-6">
                <?php
                echo $form->combobox('Select course', null, []);
                echo $form->combobox('Select schedule', null, []);
                ?>
            </div>
        </div>
    <?php endif; ?>

    <table class="table table-sticky precheck-questions-table<?= $has_custom_columns ? ' hidden' : '' ?>">
        <thead>
            <tr>
                <th scope="col" rowspan="2"><abbr title="Number">#</abbr></th>
                <th scope="col" rowspan="2"><i>I will ensure that</i></th>
                <?php foreach ($columns as $column): ?>
                    <th scope="col"<?= count($columns) == 1 ? ' style="width: 11em;"' : '' ?>>
                        <?= htmlspecialchars($column) ?>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>

        <tbody>
            <?php if ($type == 'car'): ?>
                <tr>
                    <th></th>
                    <th scope="col">Vehicle details</th>
                    <th scope="col" colspan="<?= count($columns) ?>"></th>
                </tr>

                <tr>
                    <td></td>
                    <td>Registration number</td>
                    <?php foreach ($columns as $column): ?>
                        <td><?= Form::ib_input(null, null) ?></td>
                    <?php endforeach; ?>
                </tr>

                <tr>
                    <td></td>
                    <td>Mileage</td>
                    <?php foreach ($columns as $column): ?>
                        <td><?= Form::ib_input(null, null, null, ['type' => 'number'], ['right_icon' => 'km']) ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endif; ?>

            <?php $number = 0; ?>

            <?php if (!empty($questions)) foreach ($questions as $group): ?>
                <?php if (!empty($group['name'])): ?>
                    <tr>
                        <th></th>
                        <th scope="col"><?= htmlspecialchars($group['name']) ?></th>
                        <th scope="col" colspan="<?= count($columns) ?>"></th>
                    </tr>
                <?php endif; ?>

                <?php foreach ($group['questions'] as $question_number => $question): ?>
                    <tr>
                        <td><?= ++$number ?></td>
                        <td><?= htmlspecialchars($question) ?></td>
                        <?php foreach ($columns as $column): ?>
                            <td data-stock="<?= htmlspecialchars($column) ?>">
                                <?= Form::btn_options('q_'.$number.'_'.$column, ['yes' => 'Yes', 'no' => 'No'], null, false, ['class' => 'precheck-yes_no'], ['class' => 'stay_inline']) ?>

                                <button type="button" class="precheck-view-corrective button--plain text-primary hidden">
                                    View corrective action
                                </button>
                            </td>
                        <?php endforeach; ?>
                    </tr>

                    <?php foreach ($columns as $columns_number => $column): ?>
                        <tr class="precheck-todo hidden" data-stock="<?= htmlspecialchars($column) ?>">
                            <td></td>
                            <td colspan="<?= 2 + count($columns) ?>">
                                <button type="button"
                                        class="btn-link p-0 w-100"
                                        data-toggle="collapse" data-target=".precheck-todo[data-question=<?= $question_number ?>][data-stock=<?= $columns_number ?>]" aria-expanded="false"
                                    >

                                    <h4>
                                        Flag corrective action
                                        <?php if ( $has_custom_columns): ?>
                                            - <strong><?= htmlspecialchars($column) ?></strong>
                                        <?php endif; ?>

                                        <span class="expanded-invert icon-angle-down"></span>
                                    </h4>
                                </button>

                                <div class="precheck-todo collapse" data-question="<?= $question_number ?>" data-stock="<?= $columns_number ?>">
                                    <?php
                                    echo $form->textarea('Description', null, $column." is failing the following criteria:\n".$question);
                                    ?>

                                    <div class="row gutters">
                                        <div class="col-sm-6">
                                            <?php
                                            echo $form->select('Assignees', null, []);
                                            echo $form->select('Status', null, ['Open', 'In progress', 'Done']);
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
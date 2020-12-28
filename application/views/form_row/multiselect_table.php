<div class="form-group" id="<?= $attributes['id']?>-selector-row">
    <label class="col-sm-2 control-label" for="<?= $attributes['id'] ?>-selector">
        <?php
        if ($args['allow_new'] && empty($options)) {
            echo 'Add '.lcfirst(htmlspecialchars($label));
        } else {
            echo 'Select '.lcfirst(htmlspecialchars($label));
        }
        ?>
    </label>

    <div class="col-sm-5">
        <?php
        $selector_attributes = $attributes;
        $selector_attributes['id'] = $attributes['id'].'-selector';
        if ($args['allow_new'] && empty($options)) {
            $selector_attributes['class'] = str_replace('ib-combobox', '', $selector_attributes['class']);
            echo Form::ib_input(null, 'add_'.$name, null, $selector_attributes, $args);
        } else {
            echo Form::ib_select(null, 'add_'.$name, $options, null, $selector_attributes, $args);
        }
        ?>
    </div>

    <div class="col-sm-5">
        <button type="button" class="btn btn-default form-btn" id="<?= $attributes['id'] ?>-add">Add</button>
    </div>
</div>

<div class="<?= $attributes['id'] ?>-table-wrapper" id="<?= $attributes['id'] ?>-table-wrapper">
    <table class="table table-multiselect <?= $attributes['id'] ?>-table<?= count($selected_items) ? '' : ' hidden' ?>" id="<?= $attributes['id'] ?>-table">
        <thead>
            <tr>
                <?php if (!empty($args['orderable'])): ?>
                    <th></th>

                    <th scope="col">Order</th>
                <?php endif; ?>

                <th scope="col"><?= htmlspecialchars($label) ?></th>

                <?php if (!empty($args['extra_columns'])): ?>
                    <?php foreach ($args['extra_columns'] as $column_name => $column_title): ?>
                        <?php $column_title = is_array($column_title) ? $column_title['title'] : $column_title; ?>
                        <th scope="col"><?= htmlspecialchars($column_title) ?></th>
                    <?php endforeach; ?>
                <?php endif; ?>

                <th scope="col">Remove</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($selected_items)): ?>
                <?php foreach ($selected_items as $key => $selected_item): ?>
                    <?php
                    $selected_item = is_array($selected_item) ? $selected_item : $selected_item->as_array();
                    $order = isset($selected_item['order']) ? $selected_item['order'] : $key;
                    ?>
                    <tr>
                        <?php if (!empty($args['orderable'])): ?>
                            <td><span class="icon-bars"></span></td>

                            <td>
                                <input type="hidden" class="<?= $attributes['id'] ?>-order" name="<?= $name ?>[<?= $order ?>][order]" value="<?= $order ?>" />
                                <span class="<?= $attributes['id'] ?>-order-text"><?= $order ?></span>
                            </td>
                        <?php endif; ?>

                        <td>
                            <input type="hidden" class="<?= $attributes['id'] ?>-id" name="<?= $name ?>[<?= $order ?>][<?= $args['id_field'] ?>]" value="<?= $selected_item[$args['id_field']] ?>"/>
                            <span class="<?= $attributes['id'] ?>-text"><?= htmlspecialchars($selected_item[$args['title_field']]) ?></span>
                        </td>

                        <?php if (!empty($args['extra_columns'])): ?>
                            <?php foreach ($args['extra_columns'] as $column_name => $column_title): ?>
                                <td>
                                    <?php
                                    if (isset($column_title['type']) && $column_title['type'] == 'select') {
                                        $column_data = $column_title;
                                        $column_title = $column_data['name'];
                                    } else {
                                        $column_data = ['type' => 'input'];
                                    }

                                    if (strpos($column_name, '[')) {
                                        preg_match('#(.*?)\[(.*?)\]#', $column_name, $match);

                                        // So we get 'grade[1][levels][8]', rather than 'grade[1][levels[8]]'
                                        $input_name  = $name.'['.$order.']['.$match[1].']['.$match[2].']';
                                        $input_value = !empty($selected_item[$match[1]]) ? $selected_item[$match[1]][$match[2]] : '';
                                    } else {
                                        $input_name  = $name.'['.$order.']['.$column_name.']';
                                        $input_value = isset($selected_item[$column_name]) ? $selected_item[$column_name] : '';
                                    }

                                    $input_attributes = (is_array($column_title) && isset($column_title['attributes'])) ? $column_title['attributes'] : [];
                                    $input_args       = (is_array($column_title) && isset($column_title['args']))       ? $column_title['args']       : [];

                                    if ($column_data['type'] == 'select') {
                                        $column_options = html::optionsFromRows('id', 'name', $column_data['options'], $input_value, ['label' => $column_title, 'value' => '']);
                                        echo Form::ib_select(null, $input_name, $column_options, $input_value, $input_attributes, $input_args);
                                    } else {
                                        echo Form::ib_input(null, $input_name, $input_value, $input_attributes, $input_args);
                                    }
                                    ?>
                                </td>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <td>
                            <button type="button" class="button--plain <?= $attributes['id'] ?>-delete">
                                <span class="icon-close"></span>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>

        <tfoot class="hidden" id="<?= $attributes['id'] ?>-template">
            <tr>
                <?php if (!empty($args['orderable'])): ?>
                    <td><span class="icon-bars"></span></td>
                    <td>
                        <input type="hidden" class="<?= $attributes['id'] ?>-order" name="<?= $name ?>[][order]" disabled />
                        <span class="<?= $attributes['id'] ?>-order-text"></span>
                    </td>
                <?php endif; ?>

                <td>
                    <input type="hidden" class="<?= $attributes['id'] ?>-id" name="<?= $name ?>[][<?= $args['id_field'] ?>]" disabled />
                    <input type="hidden" class="<?= $attributes['id'] ?>-title" name="<?= $name ?>[][<?= $args['title_field'] ?>]" disabled />
                    <span class="<?= $attributes['id'] ?>-title-text"></span>
                </td>

                <?php if (!empty($args['extra_columns'])): ?>
                    <?php foreach ($args['extra_columns'] as $column_name => $column_title): ?>
                        <td>
                            <?php
                            if (isset($column_title['type']) && $column_title['type'] == 'select') {
                                $column_data = $column_title;
                                $column_title = $column_data['name'];
                            } else {
                                $column_data = ['type' => 'input'];
                            }

                            if (strpos($column_name, '[')) {
                                preg_match('#(.*?)\[(.*?)\]#', $column_name, $match);
                                // So we get 'grade[1][levels][8]', rather than 'grade[1][levels[8]]'
                                $input_name  = $name.'['.$order.']['.$match[1].']['.$match[2].']';
                            } else {
                                $input_name  = $name.'['.$order.']['.$column_name.']';
                            }

                            $input_attributes = (is_array($column_title) && isset($column_title['attributes'])) ? $column_title['attributes'] : [];
                            $input_attributes['disabled'] = 'disabled';
                            $input_args       = (is_array($column_title) && isset($column_title['args']))       ? $column_title['args']       : [];

                            if ($column_data['type'] == 'select') {
                                $column_options = html::optionsFromRows('id', 'name', $column_data['options'], null, ['label' => $column_title, 'value' => '']);
                                echo Form::ib_select(null, $input_name, $column_options, null, $input_attributes, $input_args);
                            } else {
                                echo Form::ib_input(null, $input_name, null, $input_attributes, $input_args);
                            }
                            ?>
                        </td>
                    <?php endforeach; ?>
                <?php endif; ?>

                <td>
                    <button type="button" class="button--plain <?= $attributes['id'] ?>-delete">
                        <span class="icon-close"></span>
                    </button>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
<script>
    <?php
    /** This can be/should be genericised, rather than new JS embedded for each use **/
    ?>
    $(document).ready(function() {
        /** Learning outcomes **/
        var $table         = $('#<?= $attributes['id'] ?>-table');
        var $selector      = $('#<?= $attributes['id'] ?>-selector');
        var is_select_list = $selector.is('select');

        // Add learning outcome
        $('#<?= $attributes['id'] ?>-add').on('click', function() {
            var $selector_input = is_select_list ? $('#<?= $attributes['id'] ?>-selector-input') : $selector;
            var $template       = $('#<?= $attributes['id'] ?>-template').clone();
            $template.find(':disabled').prop('disabled', false);
            $template.find('[disabled]').removeAttr('disabled');

            if (is_select_list && $selector.val()) {
                // Adding an existing item (Pass an ID to the server)
                $template.find('.<?= $attributes['id'] ?>-id').val($selector.val());
                $template.find('.<?= $attributes['id'] ?>-title').val($selector.find(':selected').html());
                $template.find('.<?= $attributes['id'] ?>-title-text').html($selector.find(':selected').html());
            } else {
                // Adding a new item (Don't pass ID to the server)
                $template.find('.<?= $attributes['id'] ?>-id').remove();
                $template.find('.<?= $attributes['id'] ?>-title').val($selector_input.val());
                $template.find('.<?= $attributes['id'] ?>-title-text').text($selector_input.val());
            }

            $table.removeClass('hidden').find('tbody').append($template.html());
            update_multiselect_table_order($table);
            $selector.val('').trigger('change');
            $selector.trigger(':ib-item-added');
        });

        // When enter is pressed on the combobox input, don't submit the form. Run the "add" action instead.
        $('#<?= $attributes['id']?>-selector-row').on('keydown', '.custom-combobox-input, #<?= $attributes['id'] ?>-selector', function(ev) {
            if (ev.keyCode == 13) {
                $('#<?= $attributes['id'] ?>-add').click();
                return false;
            }
        });

        // Remove item
        $table.on('click', '.<?= $attributes['id'] ?>-delete', function() {
            $(this).parents('tr').remove();

            if ($table.find('tbody tr').length == 0) {
                $table.addClass('hidden');
            } else {
                update_multiselect_table_order($table);
            }

            $selector.trigger(':ib-item-removed');
        });

        // Make sortable
        <?php if (!empty($args['orderable'])): ?>
        $table.find('tbody').sortable({ update : function(){update_multiselect_table_order($table);} });
        <?php endif; ?>

        function update_multiselect_table_order($table)
        {
            var $rows = $table.find('tbody tr');
            var order = 1;
            $rows.each(function() {
                $(this).find('.<?= $attributes['id'] ?>-order').val(order);
                $(this).find('.<?= $attributes['id'] ?>-order-text').html(order);
                $(this).find('[name]').each(function() {
                    $(this).attr('name', $(this).attr('name').replace(/<?= $name ?>\[[0-9]*\]/, '<?= $name ?>['+order+']'));
                });
                order++;
            });
        }
    });
</script>
<link rel="stylesheet" type="text/css" href="<?= URL::get_engine_assets_base() ?>css/bootstrap.daterangepicker.min.css" />
<link rel="stylesheet" type="text/css" href="<?= URL::get_engine_assets_base() ?>js/codemirror/merged/codemirror.css" />
<style>
    .form-horizontal label {margin:unset;}
</style>
<?php
$colors = ['Amaranth', 'Amber', 'Amethyst', 'Apricot', 'Aquamarine', 'Azure', 'Baby blue', 'Beige', 'Black', 'Blue', 'Blue-green', 'Blue-violet', 'Blush', 'Bronze', 'Brown', 'Burgundy', 'Byzantium', 'Carmine', 'Cerise', 'Cerulean', 'Champagne', 'Chartreuse green', 'Chocolate', 'Cobalt blue', 'Coffee', 'Copper', 'Coral', 'Crimson', 'Cyan', 'Desert sand', 'Electric blue', 'Emerald', 'Erin', 'Gold', 'Gray', 'Green', 'Harlequin', 'Indigo', 'Ivory', 'Jade', 'Jungle green', 'Lavender', 'Lemon', 'Lilac', 'Lime', 'Magenta', 'Magenta rose', 'Maroon', 'Mauve', 'Navy blue', 'Ocher', 'Olive', 'Orange', 'Orange-red', 'Orchid', 'Peach', 'Pear', 'Periwinkle', 'Persian blue', 'Pink', 'Plum', 'Prussian blue', 'Puce', 'Purple', 'Raspberry', 'Red', 'Red-violet', 'Rose', 'Ruby', 'Salmon', 'Sangria', 'Sapphire', 'Scarlet', 'Silver', 'Slate gray', 'Spring bud', 'Spring green', 'Tan', 'Taupe', 'Teal', 'Turquoise', 'Ultramarine', 'Violet', 'Viridian', 'White', 'Yellow'];

$grouped_colors = [
    'Black'  => ['Black' => 'Black'],
    'Blue'   => ['Aquamarine' => 'Aquamarine', 'Azure' => 'Azure', 'Baby blue' => 'Baby blue', 'Blue' => 'Blue', 'Blue-green' => 'Blue-green', 'Cerulean' => 'Cerulean', 'Cobalt blue' => 'Cobalt blue', 'Cyan' => 'Cyan', 'Electric blue' => 'Electric blue', 'Navy blue' => 'Navy blue', 'Periwinkle' => 'Periwinkle', 'Persian blue' => 'Persian blue', 'Prussian blue' => 'Prussian blue', 'Sapphire' => 'Sapphire', 'Teal' => 'Teal', 'Turquoise' => 'Turquoise', 'Ultramarine' => 'Ultramarine'],
    'Brown'  => ['Bronze' => 'Bronze', 'Brown' => 'Brown', 'Chocolate' => 'Chocolate', 'Coffee' => 'Coffee', 'Copper' => 'Copper', 'Desert sand' => 'Desert sand', 'Tan' => 'Tan', 'Taupe' => 'Taupe'],
    'Green'  => ['Chartreuse green' => 'Chartreuse green', 'Emerald' => 'Emerald', 'Erin' => 'Erin', 'Green' => 'Green', 'Harlequin' => 'Harlequin', 'Jade' => 'Jade', 'Jungle green' => 'Jungle green', 'Lime' => 'Lime', 'Olive' => 'Olive', 'Pear' => 'Pear', 'Spring bud' => 'Spring bud', 'Spring green' => 'Spring green', 'Viridian' => 'Viridian'],
    'Gray'   => ['Grey' => 'Grey', 'Silver' => 'Silver', 'Slate gray' => 'Slate gray'],
    'Orange' => ['Amber' => 'Amber', 'Apricot' => 'Apricot', 'Ocher' => 'Ocher', 'Orange' => 'Orange', 'Orange-red' => 'Orange-red', 'Salmon' => 'Salmon'],
    'Pink'   => ['Blush' => 'Blush', 'Cerise' => 'Cerise', 'Magenta' => 'Magenta', 'Magenta rose' => 'Magenta rose', 'Mauve' => 'Mauve', 'Orchid' => 'Orchid', 'Pink' => 'Pink', 'Puce' => 'Puce', 'Raspberry' => 'Raspberry', 'Rose' => 'Rose'],
    'Purple' => ['Amethyst' => 'Amethyst', 'Byzantium' => 'Byzantium', 'Indigo' => 'Indigo', 'Lavender' => 'Lavender', 'Lilac' => 'Lilac', 'Plum' => 'Plum', 'Purple' => 'Purple', 'Red-violet' => 'Red-violet', 'Violet' => 'Violet'],
    'Red'    => ['Amaranth' => 'Amaranth', 'Burgundy' => 'Burgundy', 'Carmine' => 'Carmine', 'Coral' => 'Coral', 'Crimson' => 'Crimson', 'Maroon' => 'Maroon', 'Red' => 'Red', 'Ruby' => 'Ruby', 'Sangria' => 'Sangria', 'Scarlet' => 'Scarlet'],
    'White'  => ['Beige' => 'Beige', 'Champagne' => 'Champagne', 'Ivory' => 'Ivory', 'White' => 'White'],
    'Yellow' => ['Gold' => 'Gold', 'Lemon' => 'Lemon', 'Peach' => 'Peach', 'Yellow' => 'Yellow']
];
?>

<div class="form-horizontal">
    <input type="hidden" id="demo-colors" data-colors="<?= htmlspecialchars(json_encode($colors)) ?>" />

    <section>
        <h3 class="numbered-header">Inputs</h3>

        <div class="form-group vertically_center">
            <div class="col-xs-auto col-sm-4 col-md-3"><label for="demo-input-standard">Standard</label></div>

            <div class="col-xs-12 col-sm-8 col-md-6">
                <?= Form::ib_input(null, null, null, ['id' => 'demo-input-standard', 'placeholder' => 'Select colour']) ?>
            </div>

            <div class="col-xs-12 col-md-3">
                <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#demo-input-standard-code">Show code</button>
            </div>
        </div>

        <?php
        $code = '<?php
/*
 * $name - The name of the form field
 * $value - The pre-filled value of the field
 * $attributes - Array of other HTML attributes
 */

echo Form::ib_input(null, $name, $value, $attributes);
?>';
        echo View::factory('prototypes/snippets/code_block')->set('id', 'demo-input-standard-code')->set('code', $code);
        ?>

        <div class="form-group vertically_center">
            <div class="col-xs-auto col-sm-4 col-md-3"><label for="demo-input-floating_label">Floating label</label></div>

            <div class="col-xs-12 col-sm-8 col-md-6">
                <?= Form::ib_input('Select colour', null, null, ['id' => 'demo-input-floating_label']) ?>
            </div>

            <div class="col-xs-12 col-md-3">
                <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#demo-input-floating_label-code">Show code</button>
            </div>
        </div>

        <?php
        $code = '<?php
/*
 * $label - The text to appear in the floating label
 * $name - The name of the form field
 * $value - The pre-filled value of the field
 * $attributes - Array of other HTML attributes
 */

 echo Form::ib_input($label, $name, $value, $attributes);
 ?>';
        echo View::factory('prototypes/snippets/code_block')->set('id', 'demo-input-floating_label-code')->set('code', $code);
        ?>

        <div class="form-group vertically_center">
            <div class="col-xs-auto col-sm-4 col-md-3"><label for="demo-input-autocomplete">Typeselect (autocomplete)</label></div>

            <div class="col-xs-12 col-sm-8 col-md-6">
                <?php
                $attributes = [
                    'class' => 'form-autocomplete',
                    'data-autocomplete-options' => json_encode($colors),
                    'placeholder' => 'Select colour',
                    'id' => 'demo-input-autocomplete'
                ];
                echo Form::ib_input(null, null, null, $attributes);
                ?>
            </div>

            <div class="col-xs-12 col-md-3">
                <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#demo-input-autocomplete-code">Show code</button>
            </div>
        </div>

        <?php
        $code = '<?php
// This setup only works for client-side searching
/*
 * $name - The name of the form field
 * $value - The pre-filled value of the field
 * $attributes - Array of other HTML attributes
 * $ac_options - Array of options for the autocomplete
 */

$attributes = [
    \'class\' = \'form-autocomplete\',
    \'data-autocomplete-options\' => json_encode($ac_options)
];
echo Form::ib_input(null, $name, $value, $attributes);
?>';
        echo View::factory('prototypes/snippets/code_block')->set('id', 'demo-input-autocomplete-code')->set('code', $code);
        ?>

        <div class="form-group vertically_center">
            <div class="col-xs-auto col-sm-4 col-md-3"><label for="demo-input-icon">Icon</label></div>

            <div class="col-xs-12 col-sm-8 col-md-6">
                <?php
                $attributes = ['id' => 'demo-input-icon', 'placeholder' => 'Select colour'];
                $args = ['icon' => '<span class="icon-tint"></span>'];
                echo Form::ib_input(null, null, null, $attributes, $args);
                ?>
            </div>

            <div class="col-xs-12 col-md-3">
                <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#demo-input-icon-code">Show code</button>
            </div>
        </div>

        <?php
        $code = '<?php
/*
 * $name - The name of the form field
 * $value - The pre-filled value of the field
 * $attributes - Array of other HTML attributes
 * $args - additional parameters for customising the input
 * $icon - The icon (can include HTML)
 */

$args = [\'icon\' => $icon];
echo Form::ib_input(null, $name, $value, $attributes, $args);
?>';
        echo View::factory('prototypes/snippets/code_block')->set('id', 'demo-input-icon-code')->set('code', $code);
        ?>
    </section>

    <section>
        <h3 class="numbered-header">Single selects</h3>

        <div class="form-group vertically_center">
            <div class="col-xs-auto col-sm-4 col-md-3"><label for="demo-select-standard">Standard</label></div>

            <div class="col-xs-12 col-sm-8 col-md-6">
                <?= Form::ib_select(null, null, ['Select colour'] + $colors, null, ['id' => 'demo-select-standard']) ?>
            </div>

            <div class="col-xs-12 col-md-3">
                <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#demo-select-standard-code">Show code</button>
            </div>
        </div>

        <?php
        $code = '<?php
/*
 * $name - The name of the form field
 * $options - Associative array of options or a string of raw option HTML
 * $selected - The selected option
 * $attributes - Array of other HTML attributes
 */

echo Form::ib_select(null, $name, $options, $selected, $attributes);
?>';
        echo View::factory('prototypes/snippets/code_block')->set('id', 'demo-select-standard-code')->set('code', $code);
        ?>

        <div class="form-group vertically_center">
            <div class="col-xs-auto col-sm-4 col-md-3"><label for="demo-select-floating_label">Floating label</label></div>

            <div class="col-xs-12 col-sm-8 col-md-6">
                <?= Form::ib_select('Select colour', null, [''] + $colors, null, ['id' => 'demo-select-floating_label']) ?>
            </div>

            <div class="col-xs-12 col-md-3">
                <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#demo-select-floating_label-code">Show code</button>
            </div>
        </div>

        <?php
        $code = '<?php
/*
 * $label - The text to appear in the floating label
 * $name - The name of the form field
 * $options - Associative array of options or a string of raw option HTML
 * $selected - The selected option
 * $attributes - Array of other HTML attributes
 */

echo Form::ib_select($label, $name, $options, $selected, $attributes);
?>';
        echo View::factory('prototypes/snippets/code_block')->set('id', 'demo-select-floating_label-code')->set('code', $code);
        ?>

        <div class="form-group vertically_center">
            <div class="col-xs-auto col-sm-4 col-md-3"><label for="demo-select-combobox-input">Combobox</label></div>

            <div class="col-xs-12 col-sm-8 col-md-6">
                <?= Form::ib_select(null, null, ['' => ''] + $colors, null, ['class' => 'ib-combobox', 'id' => 'demo-select-combobox', 'data-placeholder' => 'Select colour']) ?>
            </div>

            <div class="col-xs-12 col-md-3">
                <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#demo-select-combobox-code">Show code</button>
            </div>

            <div class="col-sm-12 col-md-offset-3 col-md-9">
                Unlike an autocomplete, a combobox has an underlying select list. Meaning the value of the form field can be different than the text displayed. (e.g. set the field value to the ID of a course, while displaying the name of the course.)
            </div>
        </div>

        <?php
        $code = '<?php
/*
 * $name - The name of the form field
 * $options - Associative array of options or a string of raw option HTML
 * $selected - The selected option
 * $attributes - Array of other HTML attributes
 * $placeholder_text - Text to appear in the combobox
 */

$attributes = [
    \'class\' => \'ib-combobox\',
    \'data-placeholder\' => \'$placeholder_text\'
];
echo Form::ib_select(null, $name, $options, $selected, $attributes);
?>';
        echo View::factory('prototypes/snippets/code_block')->set('id', 'demo-select-combobox-code')->set('code', $code);
        ?>

        <div class="form-group vertically_center">
            <div class="col-xs-auto col-sm-4 col-md-3"><label for="demo-select-combobox-icon-input">Combobox with icon</label></div>

            <div class="col-xs-12 col-sm-8 col-md-6">
                <?= Form::ib_select(null, null, ['' => ''] + $colors, null, ['class' => 'ib-combobox', 'id' => 'demo-select-combobox-icon', 'data-placeholder' => 'Select colour'], ['icon' => '<span class="flip-horizontally"><span class="icon_search"></span></span>']) ?>
            </div>
        </div>

        <div class="form-group vertically_center">
            <div class="col-xs-auto col-sm-4 col-md-3"><label for="demo-select-single-html">HTML in options</label></div>

            <div class="col-xs-12 col-sm-8 col-md-6">
                <?php
                $color_codes = ['#f00' => 'Red', '#ff0' => 'Yellow', '#00f' => 'Blue', '#0f0' => 'Green'];
                $options = ['' => 'Select colour'];
                foreach ($color_codes as $code => $color) {
                    $options[$code] = htmlspecialchars(
                        '<span style="background: '.$code.';border: 1px solid #aaa; border-radius: 50%;display: inline-block; margin-right: .5em; width: 1em; height: 1em; position: relative; top: .1em;"></span>'.
                        '<span>'.$color.'</span>'
                    );
                }
                $args = ['multiselect_options' => ['enableHTML' => true, 'hideRadios' => true]];
                ?>
                <?= Form::ib_select(null, 'status', $options, null, ['id' => 'demo-select-single-html'], $args); ?>
            </div>
        </div>
    </section>

    <section>
        <h3 class="numbered-header">Multiple selects</h3>

        <div class="form-group vertically_center">
            <div class="col-xs-auto col-sm-4 col-md-3"><label for="demo-select-multiple">Standard</label></div>

            <div class="col-xs-12 col-sm-8 col-md-6">
                <?= Form::ib_select(null, null, $colors, null, ['multiple' => 'multiple', 'id' => 'demo-select-multiple']) ?>
            </div>
        </div>

        <div class="form-group vertically_center">
            <div class="col-xs-auto col-sm-4 col-md-3"><label for="demo-select-multiple-floating_label">Floating label</label></div>

            <div class="col-xs-12 col-sm-8 col-md-6">
                <?= Form::ib_select('Select colours', null, $colors, null, ['multiple' => 'multiple', 'id' => 'demo-select-multiple-floating_label']) ?>
            </div>
        </div>

        <div class="form-group vertically_center">
            <div class="col-xs-auto col-sm-4 col-md-3"><label for="demo-select-multiple-search">Search bar</label></div>

            <div class="col-xs-12 col-sm-8 col-md-6">
                <?php
                $attributes = ['multiple' => 'multiple', 'id' => 'demo-select-multiple-search'];
                $args = ['multiselect_options' => ['enableCaseInsensitiveFiltering' => true, 'enableFiltering' => true, 'maxHeight' => '460']];
                echo Form::ib_select('Select colours', null, $colors, null, $attributes, $args);
                ?>
            </div>
        </div>

        <div class="form-group vertically_center">
            <div class="col-xs-auto col-sm-4 col-md-3"><label for="demo-select-multiple-groups">Groups</label></div>

            <div class="col-xs-12 col-sm-8 col-md-6">
                <?php
                $attributes = ['multiple' => 'multiple', 'id' => 'demo-select-multiple-groups'];
                $args = ['multiselect_options' => [
                    'enableCaseInsensitiveFiltering' => true,
                    'enableClickableOptGroups' => true,
                    'enableFiltering' => true,
                    'includeSelectAllOption' => true,
                    'maxHeight' => 460,
                    'numberDisplayed' => 1,
                    'selectAllText' => __('ALL')
                    ]
                ];
                echo Form::ib_select(__('Select colours'), null, $grouped_colors, null, $attributes, $args);
                ?>
            </div>
        </div>
    </section>

    <section>
        <h3 class="numbered-header">Time and date pickers</h3>

        <div class="form-group vertically_center">
            <div class="col-xs-auto col-sm-4 col-md-3">
                <label for="demo-datepicker">Date picker</label>
            </div>

            <div class="col-xs-12 col-sm-8 col-md-3">
                <?= Form::ib_datepicker(null, 'date', date('Y-m-d'), [], ['id' => 'demo-datepicker']) ?>
            </div>


            <div class="col-xs-12 col-md-offset-3 col-md-3">
                <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#demo-datepicker-code">Show code</button>
            </div>
        </div>

        <?php
        $code = '<?php
// This will create an input box which shows the date in a user-friendly format.
// And a hidden field which contains the date in ISO format.
// Developers should use the ISO value for calculations and data storage.
/*
 * $label - Floating label, if any
 * $name - Name of the form field
 * $value - The date in ISO format (YYYY-MM-DD)
 * $hidden_attributes - Array of HTML attributes for the hidden field
 * $display_attributes - Array of HTML attributes for the display field
 */

echo Form::ib_datepicker($label, $name, $value, $hidden_attributes, $display_attributes);
?>';
        echo View::factory('prototypes/snippets/code_block')->set('id', 'demo-datepicker-code')->set('code', $code);
        ?>

        <div class="form-group vertically_center">
            <div class="col-xs-auto col-sm-4 col-md-3">
                <label for="demo-timepicker">Time picker</label>
            </div>

            <div class="col-xs-12 col-sm-8 col-md-3">
                <?php
                $attributes = array(
                    'autocomplete' => 'off',
                    'class'        => 'form-timepicker',
                    'id'           => 'demo-timepicker',
                );
                echo Form::ib_input(null, null, null, $attributes)
                ?>
            </div>
        </div>

        <div class="form-group vertically_center">
            <div class="col-xs-auto col-sm-4 col-md-3">
                <label for="demo-daterangepicker">Date-range picker</label>
            </div>

            <div class="col-xs-12 col-sm-8 col-md-6">
                <?php
                $start_date = date('Y-m-d');
                $end_date   = date('Y-m-d', strtotime('+1 month'));
                echo Form::ib_daterangepicker('start_date', 'end_date', $start_date, $end_date, ['id' => 'demo-daterangepicker']);
                ?>
            </div>

            <div class="col-xs-12 col-md-3">
                <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#demo-daterangepicker-code">Show code</button>
            </div>
        </div>

        <?php
        $code = '<?php
/*
 * $start_date_name - The field name for the start date
 * $end_date_name - The field name for the end date
 * $start_date - The default start date
 * $end_date - The default end date
 * $attributes - Array of other HTML attributes
 */

echo Form::ib_daterangepicker($start_date_name, $end_date_name, $start_date, $end_date, $attributes);
?>';
        echo View::factory('prototypes/snippets/code_block')->set('id', 'demo-daterangepicker-code')->set('code', $code);
        ?>
    </section>

    <section>
        <h3 class="numbered-header">Checkboxes</h3>

        <div class="form-group vertically_center">
            <div class="col-xs-auto col-sm-4 col-md-3">
                <label for="demo-checkbox-standard">Standard</label>
            </div>

            <div class="col-xs-12 col-sm-8 col-md-9">
                <?= Form::ib_checkbox('Checkbox label', null, 1, false, ['id' => 'demo-checkbox-standard']) ?>
            </div>
        </div>

        <div class="form-group vertically_center">
            <div class="col-xs-auto col-sm-4 col-md-3">
                <label for="demo-checkbox-icon">Icon</label>
            </div>

            <div class="col-xs-12 col-sm-8 col-md-9">
                <label class="checkbox-icon">
                    <input type="checkbox" id="demo-checkbox-icon" />
                    <span class="checkbox-icon-unchecked icon-ban-circle" style="font-size: 1.67em;line-height: 1;"></span>
                    <span class="checkbox-icon-checked icon-check" style="font-size: 1.67em; line-height: 1;"></span>
                </label>
            </div>
        </div>

        <div class="form-group vertically_center">
            <div class="col-xs-auto col-sm-4 col-md-3">
                <label for="demo-checkbox-switch">Switch</label>
            </div>

            <div class="col-xs-12 col-sm-8 col-md-9">
                <?= Form::ib_checkbox_switch('Checkbox label', null, 1, false, ['id' => 'demo-checkbox-switch']) ?>
            </div>
        </div>

        <div class="form-group vertically_center">
            <div class="col-xs-auto col-sm-4 col-md-3">
                <span>Buttons</span>
            </div>

            <div class="col-xs-12 col-sm-8 col-md-9">
                <?php $four_colours = ['Red', 'Yellow', 'Blue', 'Green']; ?>

                <?php foreach ($four_colours as $color): ?>
                    <label class="checkbox-icon">
                        <input type="checkbox" value="<?= $color ?>" />
                        <span class="checkbox-icon-unchecked btn btn-default"><span class="icon-times"></span> <?= $color ?></span>
                        <span class="checkbox-icon-checked   btn btn-primary"><span class="icon-plus"></span> <?=  $color ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section>
        <h3 class="numbered-header">Radio buttons</h3>

        <div class="form-group vertically_center">
            <div class="col-xs-auto col-sm-4 col-md-3">
                <label for="demo-radio-standard">Standard</label>
            </div>

            <div class="col-xs-12 col-sm-8 col-md-9">
                <?= Form::ib_radio('Radio label', null, 1, false, ['id' => 'demo-radio-standard']) ?>
            </div>
        </div>
    </section>

    <section>
        <h3 class="numbered-header">Uploader</h3>

        <div class="form-group">
            <div class="col-xs-auto col-sm-4 col-md-3">Uploader</div>

            <div class="col-xs-12 col-sm-8 col-md-6">
                <?php
                echo View::factory('multiple_upload', [
                    'browse_directory' => 'docs',
                    'directory'        => 'docs',
                    'duplicate'        => 0,
                    'include_js'       => true,
                    'single'           => true
                ]);
                ?>
            </div>

            <div class="col-xs-12 col-md-3">
                <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#demo-input-uploader-code">Show code</button>
            </div>
        </div>

        <?php
        $code = '<?php
/*
 * $browse_directory - The directory to find media in when using the "browse" button
                       Leave blank, to remove the "browse" option
 * $duplicate        - Set to 0 to bypass duplicate filename checking
 * $include_js       - Include the necessary JavaScript files for image uploading/editing
                       Set to "false" if these are already included
 * $name             - The name of the form field
 * $onsuccess        - JavaScript function to run, after the item has been uploaded
 * $preset           - The preset to save the media to, if an image
 * $single           - Set to "true" to only allow one item to be uploaded at a time
                       Set to "false" to allow multiple items to be uploaded at a time
 */
echo View::factory(\'multiple_upload\', [
    \'browse_directory\' => \'content\',
    \'duplicate\'        => 0,
    \'include_js\'       => true,
    \'name\'             => \'image\',
    \'onsuccess\'        => \'function_name\',
    \'preset\'           => \'news\',
    \'single\'           => true
]);
?>';
        echo View::factory('prototypes/snippets/code_block')->set('id', 'demo-input-uploader-code')->set('code', $code);
        ?>
    </section>

    <section class="mb-3">
        <h3 class="numbered-header">Toggle button</h3>
        <div class="btn-group btn-group-slide" data-toggle="buttons">
            <label class="btn btn-plain  active">
                <input type="radio" checked="checked" value="1" name="publish">Yes
            </label>
            <label class="btn btn-plain ">
                <input type="radio" value="0" name="publish">No
            </label>
        </div>
    </section>

    <section>
        <h3 class="numbered-header">Filter</h3>

        <div class="form-group vertically_center">
            <div class="col-xs-auto col-sm-4 col-md-3">Filter</div>

            <div class="col-xs-12 col-sm-8 col-md-6">
                <?php
                $statuses = array(
                    'pending'   => __('Pending'),
                    'approved'  => __('Approved'),
                    'declined'  => __('Declined'),
                    'cancelled' => __('Cancelled')
                );

                $options = [
                    ['name' => 'color',  'label' => 'Colour', 'options' => $colors],
                    ['name' => 'status', 'label' => 'Status', 'options' => $statuses],
                ];

                echo Form::ib_filter_menu($options);
                ?>
            </div>

            <div class="col-xs-12 col-md-3">
                <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#demo-input-uploader-code">Show code</button>
            </div>

            <?php
            $code = '<?php
/*
 * $options - Array of arrays of options to appear in the filter
 ** name      - A key in each nested array containing the field name for the option group
 ** label     - A key in each nested array for text to appear on screen for the option group
 ** options   - A key in each nested array for the list of options in the group
 */
$options = [
    [\'name\' => \'color\',  \'label\' => \'Colour\', \'options\' => $colors],
    [\'name\' => \'status\', \'label\' => \'Status\', \'options\' => $statuses],
];

echo Form::ib_filter_menu($options)
?>';
            echo View::factory('prototypes/snippets/code_block')->set('id', 'demo-input-uploader-code')->set('code', $code);
            ?>
        </div>

    </section>
</div>

<script src="<?= URL::get_engine_assets_base() ?>js/bootstrap.daterangepicker.min.js"></script>
<script src="<?= URL::get_engine_assets_base() ?>js/codemirror/merged/codemirror.js"></script>

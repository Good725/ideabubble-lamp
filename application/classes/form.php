<?php defined('SYSPATH') or die('No direct script access.');

class Form extends Kohana_Form
{

    /**
     * Creates form elements in the correct format for the cms (twitter bootstrap layout)
     *
     *     echo Form::cms('text', $data);
     *
     * @param $type
     * @param $data
     * @param array $args
     * @return string
     */
    public static function cms($type, $data, $args = [])
    {
        $function = 'cms_' . $type;

        $html  = '<div class="form-group">'."\n";

        if (!empty($args['is_microsite'])) {
            $overwrites     = !empty($args['overwrites']) ? $args['overwrites'] : [];
            $is_overwritten = (!empty($overwrites) && in_array($data['variable'], array_keys($overwrites)));

            $html .= '    <label class="col-sm-1 control-label text-center">';
            $html .= '        <input type="checkbox" name="overwrites[]" value="'.$data['variable'].'"'.($is_overwritten ? ' checked="checked"' : '').' />';
            $html .= '    </label>';

            $data['value'] = $is_overwritten ? $overwrites[$data['variable']] : $data['value'];
        }

        $html .= '    <label for="'.$data['variable'].'" class="col-sm-2 control-label text-left">'.$data['name'].'</label>'."\n";
        $html .= '    '.Form::$function($data)."\n";
        $html .= '</div>'."\n";

        return $html;
    }

    private static function cms_datetime($data)
    {

        $html  = '<div class="col-sm-4">';
        $html .= '<input type="text" id="';
        $html .= $data['variable'];
        $html .= '" name="';
        $html .= $data['variable'] . '" class="form-control datetime ';

        // Add the note, if it is supplied
        if ($data['note']) {
            $html .= ' popinit" data-content="';
            $html .= $data['note'];
            $html .= '" rel="popover" data-trigger="focus hover" data-original-title="';
            $html .= $data['name'];
        }

        $html .= '" value="'.HTML::chars($data['value']).'"';
        $html .= ( ! empty($data['readonly'])) ? ' readonly="readonly"' : '';
        $html .= '/>';
        $html .= '</div><script>$(document).on("ready", function(){$("#' . $data['variable'] . '").datetimepicker({format: "Y-m-d H:i:s"});})</script>';

        return $html;

    }

    private static function cms_date($data)
    {

        $html = '<div class="col-sm-4"><input type="text" id="';
        $html .= $data['variable'];
        $html .= '" name="';
        $html .= $data['variable'] . '" class="form-control date';

        // Add the note if it is supplied
        if ($data['note']) {
            $html .= ' popinit" data-content="';
            $html .= $data['note'];
            $html .= '" rel="popover" data-trigger="focus hover" data-original-title="';
            $html .= $data['name'];
        }

        $html .= '" value="'.HTML::chars($data['value']).'"';
        $html .= ( ! empty($data['readonly'])) ? ' readonly="readonly"' : '';
        $html .= '/>';
        $html .= '<script>$(document).on("ready", function(){$("#' . $data['variable'] . '").datetimepicker({format: "Y-m-d", timepicker:false});})</script>';

        return $html;

    }

    /*
     * HTMl for the text input
     */
    private static function cms_text($data)
    {

        $html = '<div class="col-sm-4"><input type="text" id="';
        $html .= $data['variable'];
        $html .= '" name="';
        $html .= $data['variable'] . '" class="form-control';

        // Add the note if it is supplied
        if ($data['note']) {
            $html .= ' popinit" data-content="';
            $html .= $data['note'];
            $html .= '" rel="popover" data-trigger="focus hover" data-original-title="';
            $html .= $data['name'];
        }

        $html .= '" value="'.HTML::chars($data['value']).'"';
		$html .= ( ! empty($data['readonly'])) ? ' readonly="readonly"' : '';
        $html .= '/>';
        $html .= '</div>';

        return $html;

    }

    /*
     * HTML for the password input
     */
    private static function cms_password($data)
    {

        $html = '<div class="col-sm-4"><input type="password" id="';
        $html .= $data['variable'];
        $html .= '" name="';
        $html .= $data['variable'] . '" class="form-control';

        // Add the note if it is supplied
        if ($data['note']) {
            $html .= ' popinit" data-content="';
            $html .= $data['note'];
            $html .= '" rel="popover" data-trigger="focus hover" data-original-title="';
            $html .= $data['name'];
        }

        $html .= '" value="'.HTML::chars($data['value']).'"';
		$html .= ( ! empty($data['readonly'])) ? ' readonly="readonly"' : '';
        $html .= '/>';
        $html .= '</div>';

        return $html;

    }

    /*
     * HTMl for the textarea
     */
    private static function cms_textarea($data)
    {

        $html  = '<div class="col-sm-4">';
        $html .= '<textarea rows="3" id="' . $data['variable'] . '" name="' . $data['variable'] . '" class="form-control';

        // Add the note if it is supplied
        if ($data['note']) {
            $html .= ' popinit" data-content="' . $data['note'] . '" rel="popover" data-trigger="focus hover" data-original-title="' . $data['name'];
        }

        $html .= '"';
		$html .= ( ! empty($data['readonly'])) ? ' readonly="readonly"' : '';
		$html .='>' . HTML::chars($data['value']) . '</textarea>';
        $html .= '</div>';

        return $html;

    }

    /*
     * HTMl for the WYSIWYG editor
     */
    private static function cms_wysiwyg($data)
    {
        $html  = '<div class="col-sm-7">';
        $html .= '<p>'.$data['note'].'</p>';
        $html .= '<textarea id="' . $data['variable'] . '" name="' . $data['variable'] . '" class="form-control ckeditor';
        $html .= '"';
		$html .= ( ! empty($data['readonly'])) ? ' readonly="readonly"' : '';
		$html .='>' . HTML::chars($data['value']) . '</textarea>';
        $html .= '</div>';

        return $html;
    }

    private static function cms_html_editor($data)
    {
        $html  = '<div class="col-sm-7">';
        $html .= '<p>'.$data['note'].'</p>';
        $html .= '<textarea id="' . $data['variable'] . '" name="' . $data['variable'] . '" class="form-control code_editor" rows="20" data-mode="xml"';
        $html .= ( ! empty($data['readonly'])) ? ' readonly="readonly"' : '';
        $html .='>' . HTML::chars($data['value']) . '</textarea>';
        $html .= '</div>';

        return $html;
    }

	/*
	 * HTML for colour picker
	 */
	private static function cms_color_picker($data)
	{
		$html  = '<div class="col-sm-4">';
		$html .= '<input type="text" id="'.$data['variable'].'" name="'.$data['variable'].'" class="form-control color_picker_input';

		// Add the note if it is supplied
		if ($data['note'])
		{
			$html .= ' popinit" data-content="'.$data['note'].'" rel="popover" data-trigger="focus hover" data-original-title="'.$data['name'];
		}

		$html .= '" value="'.HTML::chars($data['value']).'" /></div><div class="col-sm-4">';
		$html .= View::factory('content/settings/color_picker')->set(array('data' => $data));
		$html .= '</div>'; // .col-sm-4

		return $html;
	}

	/*
     * HTMl for the checkboxes
     */
    private static function cms_checkbox($data)
    {
        if ($data['value'] === 'TRUE' OR ($data['value'] == '' AND $data['default'] === 'TRUE')) {
            $checked = 'checked="checked"';
        } else {
            $checked = '';
        }

        $html  = '<div class="col-sm-4">';
        $html .= '<label class="checkbox-setting">';
        $html .= '<input type="hidden" name="' . $data['variable'] . '" value="FALSE" />';
        $html .= '<input type="checkbox" id="' . $data['variable'] . '" name="' . $data['variable'] . '" class="" value="TRUE" ' . $checked . ' /> ';

        // Add the note if it is supplied
        if ($data['note']) {
            $html .= $data['note'];
        }

        $html .= '</label>';
        $html .= '</div>';

        return $html;

    }

    /*
     * HTMl for the select inputs
     * This is different to cms_dropdown, this allows raw <option> input.
     */
    private static function cms_select($data)
    {
        $html  = '<div class="col-sm-4">';
        $html .= '<select id="' . $data['variable'] . '" name="' . $data['variable'] . '" class="form-control';
        if ($data['note']) {
            $html .= ' popinit" data-content="' . $data['note'] . '" rel="popover" data-trigger="focus hover" data-original-title="' . $data['name'];
        }
        $html .= '" >';
        $html .= $data['options'];
        $html .= '</select>';
        $html .= '</div>';
        return $html;
    }

	/*
	 * HTML for combobox inputs
	 */
	private static function cms_combobox($data)
	{
		$html  = '<div class="col-sm-4">';
		$html .= '<select id="'.$data['variable'].'" name="'.$data['variable'].'" class="form-control ib-combobox';
		if ($data['note']) {
			$html .= ' popinit" data-content="'.$data['note'].'" rel="popover" data-trigger="focus hover" data-original-title="'.$data['name'];
		}
		$html .= '" >';
		$html .= $data['options'];
		$html .= '</select>';
		$html .= '</div>';
		return $html;
	}

    /*
     * HTML for multiselect inputs
     */
    private static function cms_multiselect($data)
    {
        $name = $data['variable'].'[]';
        $selected = unserialize($data['value']) ? unserialize($data['value']) : $data['value'];
        $attributes = ['multiple' => 'multiple', 'id' => $data['variable']];
        $args['multiselect_options'] = [
            'includeSelectAllOption' => true,
            'maxHeight' => 460,
            'selectAllText' => __('ALL')
        ];

        $html  = '<div class="col-sm-4">';

        // If the multiselect is empty, the value of the hidden field will be sent to the server.
        $html .= '<input type="hidden" name="'.$name.'" value="" />';

        if ($data['note']) {
            $html .= '<div class="popinit" data-content="'.$data['note'].'" rel="popover" data-trigger="focus hover" data-original-title="'.$data['name'].'">';
        }
        $html .= Form::ib_select(null, $name, $data['options'], $selected, $attributes, $args);
        if ($data['note']) {
            $html .= '</div>';
        }
        $html .= '</div>';

        return $html;
    }


    /*
     * HTMl for the dropdown inputs
     */
    private static function cms_dropdown($data)
    {
        $html  = '<div class="col-sm-4">';
        $html .= '<select id="' . $data['variable'] . '" name="' . $data['variable'] . '" class="form-control';

        // Add the note if it is supplied
        if ($data['note']) {
            $html .= ' popinit" data-content="' . $data['note'] . '" rel="popover" data-trigger="focus hover" data-original-title="' . $data['name'];
        }

        $html .= '" >';

        if (is_array($data['options'])) {
            foreach ($data['options'] as $value => $name) {
                // Check which option is the selected one
                if ($value === $data['value']) {
                    $html .= '<option value="' . $value . '" selected="selected" >' . HTML::chars($name) . '</option>';
                } else {
                    $html .= '<option value="' . $value . '" >' . HTML::chars($name) . '</option>';
                }
            }
        }

        $html .= '</select>';
        $html .= '</div>';

        return $html;
    }


    /*
     * HTML for toggle buttons
     */
    private static function cms_toggle_button($data)
    {
		$slider = (is_array($data['options']) AND count($data['options']) == 2);

        $html  = '<div class="col-sm-7">';
		$html .= '<div'.($slider ? '' : ' data-toggle="buttons"').' id="'.$data['variable'].'" class="'.($slider ? '' : 'btn-group').' left';

        if ($data['note'])
        {
            $html .= ' popinit" data-content="';
            $html .= $data['note'];
            $html .= '" rel="popover" data-trigger="focus hover" data-original-title="';
            $html .= $data['name'];
        }

        $html .= '">';

		if ($slider)
		{
			$html .= html::toggle_button(
				$data['variable'],
				$data['options'][0]['label'],
				$data['options'][1]['label'],
				($data['options'][0]['value'] == $data['value'])
			);
		}
		elseif (is_array($data['options']))
        {
            foreach ($data['options'] as $option)
            {
                $html .= '<label class="btn btn-default'.(($option['value'] == $data['value']) ? ' active': '').'">';
                $html .= '<input type="radio" name="'.$data['variable'].'" value="'.$option['value'].'"'.(($option['value'] == $data['value']) ? ' checked="checked"': '').' />';
                $html .= $option['label'].'</label>';
            }
		}
		$html .= '</div><!-- id          -->';
        $html .= '</div><!-- .col-sm-9   -->';

		$html = str_replace(' class=""', '', $html);

        return $html;
    }

    public static function ib_input($label, $name, $value = NULL, $attributes = NULL, $args = NULL)
    {
        $attributes['name']            = $name;
        $attributes['value']           = $value;
        $attributes['type']            = isset($attributes['type'])            ? $attributes['type']            : 'text';
        $attributes['placeholder']     = isset($attributes['placeholder'])     ? $attributes['placeholder']     : (trim($label) ? trim($label).':' : '');
        $args['icon']                  = isset($args['icon'])                  ? $args['icon']                  : null;
        $args['invert_icon']           = isset($args['invert_icon'])           ? $args['invert_icon']           : false;
        $args['icon_attributes']       = isset($args['icon_attributes'])       ? $args['icon_attributes']       : array();
        $args['right_icon']            = isset($args['right_icon'])            ? $args['right_icon']            : null;
        $args['invert_right_icon']     = isset($args['invert_right_icon'])     ? $args['invert_right_icon']     : false;
        $args['right_icon_attributes'] = isset($args['right_icon_attributes']) ? $args['right_icon_attributes'] : array();

        $args['icon_position']         = (isset($args['icon_position']) && strtolower($args['icon_position']) == 'right') ? 'right' : 'left'; // deprecated use 'icon_right' instead
        $args['fullwidth']             = ( ! isset($args['fullwidth']) || $args['fullwidth'] != false);
        $args['type_select']           = ( ! empty ($args['type_select']));
        $args['required']              = ((isset($attributes['class']) && strpos($attributes['class'], 'validate[required') > -1) || ( ! empty($args['required'])));
        $args['colorpicker']           = ((isset($attributes['class']) && strpos($attributes['class'], 'colorpicker') > -1) || ( ! empty($args['colorpicker'])));

        if ($args['icon'] == 'search') {
            $args['icon'] = '<span class="flip-horizontally"><span class="icon_search"></span></span>';
        }

        if ($args['right_icon'] == 'search') {
            $args['right_icon'] = '<span class="flip-horizontally"><span class="icon_search"></span></span>';
        }

        if ($args['required'] && $attributes['placeholder']) {
            $attributes['placeholder'] .= '*';
        }

        if (isset($attributes['disabled']) && $attributes['disabled'] === false) {
            unset($attributes['disabled']);
        }

        if (isset($attributes['readonly']) && $attributes['readonly'] === false) {
            unset($attributes['readonly']);
        }

        $attributes['placeholder'] = ($attributes['placeholder'] == ':') ? '' : $attributes['placeholder'];

        // If there is no label, the 'form-input' class is added directly to the input field
        if ( ! trim($label)) {
            $attributes['class']  = 'form-input'.(isset($attributes['class']) ? ' '.$attributes['class'] : '');
            $attributes['class'] .= ( ! $args['fullwidth']) ? ' autowidth' : '';
        }

        if ($args['icon']) {
            $args['icon_attributes']['class'] = 'input_group-icon'.( ! empty($args['invert_icon']) ? ' inverse' : '').(isset($args['icon_attributes']['class']) ? ' '.$args['icon_attributes']['class'] : '');
        }

        if ($args['right_icon']) {
            $args['right_icon_attributes']['class'] = 'input_group-icon'.( ! empty($args['invert_right_icon']) ? ' inverse' : '').(isset($args['right_icon_attributes']['class']) ? ' '.$args['right_icon_attributes']['class'] : '');
        }

        if ($attributes['type'] == 'password' && !empty($args['password_meter'])) {
            $attributes['class'] = (isset($attributes['class']) ? $attributes['class'].' ' : '').'password-with_meter';
        }

        // If the field is required, add the validation class, unless a validation class has been set manually.
        if ($args['required'] && strpos($attributes['class'], 'validate[') === false) {
            self::add_class($attributes, 'validate[required]');
        }

        // Legacy support. Instances of 'icon' => 'x', 'icon_position' => 'right' should be replaced with 'right_icon' => 'x'
        if ($args['icon_position'] == 'right') {
            $args['right_icon'] = $args['icon'];
            $args['right_icon_attributes'] = $args['icon_attributes'];
            $args['icon'] = null;
        }
        return View::factory('snippets/ib_input')->set(array(
            'label'      => $label,
            'attributes' => $attributes,
            'args'       => $args,
            'is_active'  => (trim($value) !== '' && $value !== null)
        ));
    }

    public static function ib_select($label, $name, $options = null, $selected = null, $attributes = array(), $args = null)
    {
        $attributes['name']  = $name;
        $is_combobox         = (isset($attributes['class']) && (strpos($attributes['class'], 'ib-combobox') !== false));
        $is_multiple         = ( ! empty($attributes['multiple']));
        $required            = ((isset($attributes['class']) AND strpos($attributes['class'], 'validate[required') > -1) OR ( ! empty($args['required'])));
        $multiselect_options = (!empty($args['multiselect_options'])) ? ' data-multiselect_options="'.htmlentities(json_encode($args['multiselect_options'])).'"' : '';


        if ($is_combobox && !empty($args['allow_new'])) {
            self::add_class($attributes, 'ib-combobox-new');
            if (empty($attributes['data-placeholder']) && $label) {
                $attributes['data-placeholder'] = 'Select a '.strtolower($label).' or type a new one';
            }
        }

        // Dropdown arrow is on the right by default. It can be moved to the left or removed completely.
        $arrow_position = '';
        if (isset($args['arrow_position'])) {
            if ($args['arrow_position'] === false || strtolower($args['arrow_position']) == 'none') {
                $arrow_position = ' form-select--no_arrow';
            }
            else if (strtolower($args['arrow_position']) == 'left') {
                $arrow_position = ' form-select--left_arrow';
            }
        }

        if (isset($attributes['disabled']) && $attributes['disabled'] === false) {
            unset($attributes['disabled']);
        }

        if (!$label) {
            $attributes['class'] = 'form-input'.(isset($attributes['class']) ? ' '.$attributes['class'] : '');
        }

        if (is_string($options)) {
            if (!empty($args['please_select'])) {
                $options = '<option value="">'.($is_combobox ? '' : '-- Please select --').'</option>'.$options;
            }

            $select_html = '<select'.HTML::attributes($attributes).'>'.$options.'</select>';
        }
        else {
            if (!empty($args['please_select'])) {
                $options = ['' => $is_combobox ? '' : '-- Please select --'] + $options;
            }
            $select_html = self::select($name, $options, $selected, $attributes);
        }

        if ($label) {
            // Mark field as "active", if there is text inside the selected option
            preg_match('/<option(.*?)selected(.*?)>(.*?)<\/option>/s', $select_html, $match);
            if (!$match && !$is_multiple) {
                // If no option has the "selected" attribute and this is not a multiselect, the first option is selected by default
                preg_match('/<option(.*?)(.*?)>(.*?)<\/option>/s', $select_html, $match);
            }
            $selected_text = isset($match[3]) ? trim($match[3]) : '';

            // Multiple is also put in the "active" state, because the text "none selected" appears when empty.
            $is_active = ($selected_text !== '' || $is_multiple);
        }

        return View::factory('snippets/ib_select')
            ->set('args', $args)
            ->set('arrow_position', $arrow_position)
            ->set('label', $label)
            ->set('is_active', !empty($is_active))
            ->set('is_multiple', $is_multiple)
            ->set('multiselect_options', $multiselect_options)
            ->set('required', $required)
            ->set('select_html', $select_html)
            ;
    }

    public static function ib_checkbox($label, $name, $value = NULL, $checked = FALSE, $attributes = NULL, $label_position = 'right')
    {
        $is_required = ((isset($attributes['class']) AND strpos($attributes['class'], 'validate[required') > -1));

        if (isset($attributes['disabled']) && $attributes['disabled'] === false) {
            unset($attributes['disabled']);
        }

        if (isset($attributes['readonly']) && $attributes['readonly'] === false) {
            unset($attributes['readonly']);
        }

        $is_disabled = (isset($attributes['disabled']));
        $is_readonly = (isset($attributes['readonly']));

        $label_html = trim($label) ? '<span class="form-checkbox-label'.($is_required ? ' label--mandatory' : '').'">'.$label.'</span>' : '';
        return '
            <label class="form-checkbox'.($is_disabled ? ' disabled' : '').($is_readonly ? ' readonly' : '').'">
                '.(($label_position == 'left') ? $label_html : '').'
                '.self::checkbox($name, $value, $checked, $attributes).'
                <span class="form-checkbox-helper"></span>
                '.(($label_position != 'left') ? $label_html : '').'
            </label>';
    }

    public static function ib_checkbox_switch($label, $name, $value= null, $checked = false, $attributes = null, $label_position = 'left')
    {
        $is_required = ((isset($attributes['class']) AND strpos($attributes['class'], 'validate[required') > -1));
        $is_disabled = (isset($attributes['disabled']));
        $is_readonly = (isset($attributes['readonly']));

        $label_html = trim($label) ? '<span class="checkbox-switch-label'.($is_required ? ' label--mandatory' : '').'">'.$label.'&nbsp; </span>' : '';
        return '
            <label class="checkbox-switch'.($is_disabled ? ' disabled' : '').($is_readonly ? ' readonly' : '').'">
                '.(($label_position == 'left') ? $label_html : '').'
                '.self::checkbox($name, $value, $checked, $attributes).'
                <span class="checkbox-switch-helper"></span>
                '.(($label_position != 'left') ? $label_html : '').'
            </label>';
    }

    public static function ib_radio($label, $name, $value = NULL, $checked = FALSE, $attributes = NULL, $label_position = 'right')
    {
        $is_disabled = (isset($attributes['disabled']));
        $is_readonly = (isset($attributes['readonly']));

        $label_html = trim($label) ? '<span class="form-radio-label">'.$label.'</span>' : '';
        return '
            <label class="form-radio'.($is_disabled ? ' disabled' : '').($is_readonly ? ' readonly' : '').'">
                '.(($label_position == 'left') ? $label_html : '').'
                '.self::radio($name, $value, $checked, $attributes).'
                <span class="form-radio-helper"></span>
                '.(($label_position != 'left') ? $label_html : '').'
            </label>';
    }

    public static function ib_textarea($label, $name, $body = '', $attributes = NULL, $double_encode = TRUE)
    {
        $attributes['name'] = $name;
        $is_active = (trim($body) !== '' AND $body !== NULL);

        if (trim($label))
        {
            $attributes['placeholder'] = isset($attributes['placeholder']) ? $attributes['placeholder'] : $label.':';
            $attributes['placeholder'] = ($attributes['placeholder'] == ':') ? '' : $attributes['placeholder'];

            $is_required = ((isset($attributes['class']) AND strpos($attributes['class'], 'validate[required') > -1));
            return '
                <label class="form-input form-input--textarea form-input--pseudo'.($is_active ? ' form-input--active' : '').'">
                    <span class="form-input--pseudo-label'.($is_required ? ' label--mandatory' : '').'">'.$label.'</span>
                    <textarea'.HTML::attributes($attributes).'>'.HTML::chars($body, $double_encode).'</textarea>
                </label>';
        }
        else
        {
            // If there is no label, add the 'form-input' class directly to the textarea element
            $attributes['class'] = 'form-input form-input--textarea'.(isset($attributes['class']) ? ' '.$attributes['class'] : '');
            return '<textarea'.HTML::attributes($attributes).'>'.HTML::chars($body, $double_encode).'</textarea>';
        }
    }

    public static function ib_datepicker($label = null, $name = null, $value = null, $hidden_attributes = [], $display_attributes = [], $args = [])
    {
        // Regular text input, but with a separate hidden field for the ISO-format date. Always use this date for calculations and saving data.
        // The text input will display the date in a fancy format. This is only for display purposes.

        $date_format = !empty($display_attributes['data-date_format']) ? $display_attributes['data-date_format'] : 'd/M/Y';

        // Add datepicker class
        $display_attributes['class'] = (isset($display_attributes['class']) ? $display_attributes['class'].' ' : '') . 'form-datepicker';

        // Add format as a data attribute, so the JS knows to use the same format.
        $display_attributes['data-date_format'] = $date_format;

        // Turn off autocomplete. It can interfere with the datepicker.
        $display_attributes['autocomplete'] = isset($display_attributes['autocomplete']) ? $display_attributes['autocomplete'] : 'off';

        $hidden_attributes['type']  = isset($hidden_attributes['type']) ? $hidden_attributes['type'] : 'hidden';
        $hidden_attributes['name']  = $name;
        $hidden_attributes['value'] = $value;
        $hidden_attributes['class'] = (isset($hidden_attributes['class']) ? $hidden_attributes['class'].' ' : '') . 'form-datepicker-iso';

        if (isset($display_attributes['id']) && !isset($hidden_attributes['id'])) {
            $hidden_attributes['id'] = $display_attributes['id'].'-iso';
        }

        $formatted_date = $value ? date($date_format, strtotime($value)) : '';

        $return  = '<span class="form-datepicker-wrapper">';
        $return .= '    <input'.HTML::attributes($hidden_attributes).'/>';
        $return .= self::ib_input($label, null, $formatted_date, $display_attributes, $args);
        $return .= '</span>';

        return $return;
    }

    public static function ib_daterangepicker($start_name, $end_name, $start_date = null, $end_date = null, $attributes = [], $args = [])
    {
        return View::factory('snippets/daterangepicker')
            ->set([
                'start_name' => $start_name,
                'end_name'   => $end_name,
                'start_date' => $start_date,
                'end_date'   => $end_date,
                'attributes' => $attributes,
                'args'       => $args
            ]);
    }

    public static function ib_filter_menu($options = [])
    {
        return View::factory('form_filter', compact('options'));
    }

    public static function btn_options($name, $options, $selected = null, $multiselect = false, $input_attributes = array(), $attributes = array(), $args = [])
    {
        $input_attributes['name'] = $name;
        $input_attributes['type'] = $multiselect ? 'checkbox' : 'radio';

        $attributes['class'] = 'btn-group btn-group-pills btn-group-pills-regular'.(isset($attributes['class']) ? ' '.$attributes['class'] : '');

        // If $attributes['id'] or $input_attributes['id'] is blank, assume one based on the other
        if (empty($input_attributes['id']) && !empty($attributes['id'])) {
            $input_attributes['id'] = $attributes['id'];
        }

        if (empty($attributes['id']) && !empty($input_attributes['id'])) {
            $attributes['id'] = $input_attributes['id'];
        }

        return View::factory('snippets/btn_options')->set(array(
            'attributes'       => $attributes,
            'input_attributes' => $input_attributes,
            'name'             => $name,
            'options'          => $options,
            'selected'         => $selected,
            'selected_class'   => (isset($args) && isset($args['selected_class'])) ? trim($args['selected_class']) : 'btn-primary',
            'type'             => $input_attributes['type']
        ));
    }

    // Deprecated? We can directly use the IbForm one.
    public static function add_class(&$attributes, $class)
    {
        IbForm::add_class($attributes, $class);
    }

} // End Form

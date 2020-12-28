<?php defined('SYSPATH') or die('No direct script access.');

class IbForm
{
    public $id = '';            // HTML ID of the form. This is also prefixed to the IDs of fields throughout the form
    public $action = '';        // The action to run when the form is submitted
    public $attributes = [];    // Additional attributes for the <form></form>
    public $cancel_url = '';    // URL to run when the "Cancel" button is clicked
    public $delete_url = '';    // URL to run when the "Delete" button is clicked (and confirmed)
    public $data_object = null; // An object containing data to prepopulate form fields
    public $delete_permission = ''; // Permission needed to use the delete button, if any
    public $layout = 'horizontal';  // 'horizontal' = labels next to fields, 'vertical' = labels above fields
    public $method = '';        // The method of the form (get or post)
    public $name_field = '';    // The name of the database column for the main name/title field
    public $type = '';          // Word to describe the type of item, whose data the form is editing

    // For grid customisation
    public $label_size       = 'col-sm-2';
    public $input_size_small = 'col-sm-5';
    public $input_size_large = 'col-sm-10';


    public $filling_tab = false;
    public $tabs = [];

    /**
     * @param $id            - the HTML ID of the form
     * @param string $action - the form action
     * @param string $method = the form method get/post
     */
    public function __construct($id, $action = '', $method = 'post', $args = [])
    {
        $this->id = $id;
        $this->action = $action;
        $this->method = $method;
        $this->name_field = 'title';
        $this->published_field = 'published';
        $this->data_object = new stdClass();

        if (!empty($args['layout']) && $args['layout'] == 'vertical') {
            $this->layout           = 'vertical';
            $this->label_size       = 'col-sm-12 text-left';
            $this->input_size_small = 'col-sm-12';
            $this->input_size_large = 'col-sm-12';
        }
    }

    /**
     * Load an object containing data that will be used to populate the form
     *
     * @param $object
     */
    public function load_data($object)
    {
        // If an array is passed in, convert it to an object
        if (is_array($object)) {
            $array  = $object;
            $object = new stdClass();

            foreach ($array as $key => $value) {
                $object->$key = $value;
            }
        }

        $this->data_object = $object;

        if (!$this->name_field) {
            // Assume the name field is "title", unless the data object has a "name" property
            $this->name_field = (property_exists($object, 'name')) ? 'name' : 'title';
        }

        if (method_exists($object, 'get_publish_column') && $object->get_publish_column()) {
            $this->published_field = $object->get_publish_column();
        }
        elseif (property_exists($object, 'published')) {
            $this->published_field = 'published';
        }
        elseif (property_exists($object, 'publish')) {
            $this->published_field = 'publish';
        }
        else {
            $this->published_field = false;
        }
    }

    // Find a default value for a field, by searching the loaded data object
    public function find_value($name, &$value)
    {
        if ($value === null && isset($this->data_object) && isset($this->data_object->{$name})) {
            $value = $this->data_object->{$name};
        }
    }

    // Form an ID using the form ID and the field name
    public function create_id(&$attributes, $name)
    {
        if (!isset($attributes['id'])) {
            $attributes['id'] = $this->id.'-'.self::string_to_id($name);
        }
    }

    public static function string_to_id($string)
    {
        return preg_replace('/[^a-zA-Z-_]/','', str_replace(' ', '_', str_replace('[', '-', strtolower($string))));
    }

    // Add a class to a set of attributes
    public static function add_class(&$attributes, $class)
    {
        if (empty($attributes['class'])) {
            $attributes['class'] = $class;
        }
        // Other classes exist => append the new class to the value, if it is not already in the value
        else if (strpos(' '.$attributes['class'].' ', ' '.$class.' ') === false) {
            $attributes['class'] .= ' '.$class;
        }
    }

    // Get attributes for rendering an popover
    public static function popover_attributes($args = [])
    {
        if (!empty($args['popover'])) {
            return html::attributes([
                'rel'          => 'popover',
                'data-trigger' => 'hover',
                'data-content' => $args['popover']
            ]);
        } else {
            return '';
        }
    }

    public function has_delete_button()
    {
        $url_exists     = !empty($this->delete_url);
        $has_permission = (!$this->delete_permission || Auth::instance()->has_access($this->delete_permission));
        $object_exists  = !empty($this->data_object->id);

        return ($url_exists && $has_permission && $object_exists);
    }

    /*
     * ==============================================
     * # Form HTML rendering functions
     * ==============================================
     */

    public function start($args = [])
    {
        // No other classes exist => set the the new class as the value
        $attributes = $this->attributes;
        $attributes['method'] = $this->method;
        $attributes['action'] = $this->action;
        $attributes['id']     = $this->id;
        self::add_class($attributes, 'form-horizontal');
        self::add_class($attributes, 'validate-on-submit');

        $return  = '<form'.html::attributes($attributes).'>';

        $published = ($this->published_field && isset($this->data_object->{$this->published_field})) ? $this->data_object->{$this->published_field} : true;
        // Empty string and null are default values, not values from the database, so default to "published" when these are used
        $published = ($published === '' || $published === null) ? true : $published;

        if (!isset($args['title']) || $args['title'] !== false) {
            $return .= View::factory('form_title')
                ->set([
                    'name'            => isset($this->data_object) && isset($this->data_object->{$this->name_field}) ? $this->data_object->{$this->name_field} : '',
                    'name_field'      => $this->name_field,
                    'name_attributes' => [
                        'class' => 'validate[required]',
                        'placeholder' => $this->type ? ('Enter '.$this->type.' title') : 'Enter title'
                    ],
                    'publish_field'   => $this->published_field,
                    'published'       => $published
                ]);
        }

        return $return;
    }

    public function end()
    {
        return View::factory('form_row/form_end')->set('form', $this);
    }

    /*
     * Use tab_start() to define tab content and tabs() to print tabs after they have been defined.
     *
     * For example:
     *
     * <?php $form->tab_start('Details', true) ?>
     *      <p>Details</p>
     *
     * <?php $form->tab_start('Advanced') ?>
     *      <p>Advanced</p>
     *
     * <?= $form->tabs() ?>
     *
     *
     * @param string $name   - The name displayed in the tab
     * @param bool   $active - If this tab is to be opened by default
     */
    public function tab_start($name, $active = false, $tab_id = '', $class = '')
    {
        if ($this->filling_tab !== false) {
            $this->tabs[$this->filling_tab]['content'] = ob_get_clean();
        }

        $this->filling_tab = $name;
        $this->tabs[$name] = ['content' => '', 'active' => $active, 'id' => $tab_id, 'class' => $class];
        ob_start();
    }

    /**
     * Takes tab details defined using tab_start() and prints them as tabs
     * See the tab_start() documentation for more information
     */
    public function tabs()
    {
        $this->tabs[$this->filling_tab]['content'] = ob_get_clean();
        $tabs = $this->tabs;

        // Reset tabs
        $this->tabs = [];
        $this->filling_tab = false;

        return View::factory('form_row/tabs')->set('form', $this)->set('tabs', $tabs);
    }

    /* Create a hidden field */
    public function hidden($name, $value = null, $attributes = [])
    {
        self::create_id($attributes, $name);
        self::find_value($name, $value);
        $attributes['type']  = 'hidden';

        return Form::input($name, $value, $attributes);
    }

    /* Create a text input */
    public function input($label, $name, $value = null, $attributes = [], $args = [])
    {
        self::create_id($attributes, $name);
        self::find_value($name, $value);

        return View::factory('form_row/input')->set([
            'form' => $this,
            'label' => $label,
            'name' => $name,
            'value' => $value,
            'attributes' => $attributes,
            'args' => $args
        ]);
    }

    public function numeric_input($label, $name, $value = null, $attributes = [], $args = [])
    {
        $attributes['type'] = 'number';
        return self::input($label, $name, $value, $attributes, $args);
    }

    public function email($label, $name, $value = null, $attributes = [], $args = [])
    {
        $attributes['type'] = 'email';
        self::add_class($attributes, 'validate[custom[email]]');
        return self::input($label, $name, $value, $attributes, $args);
    }

    public function phone($label, $name, $value = null, $attributes = [], $args = [])
    {
        self::add_class($attributes, 'validate[custom[phone]]');
        return self::input($label, $name, $value, $attributes, $args);
    }

    public function datepicker($label, $name, $value = null, $hidden_attributes = [], $display_attributes = [], $args = [])
    {
        $attributes['autocomplete'] = 'off';
        self::create_id($hidden_attributes, $name);
        self::find_value($name, $value);

        if (empty($display_attributes['id'])) {
            $display_attributes['id'] = $hidden_attributes['id'].'-display';
        }

        if (empty($args['icon'])) {
            $args['icon'] = '<span class="flaticon-calendar"></span>';
        }

        return View::factory('form_row/datepicker')->set([
            'form' => $this,
            'label' => $label,
            'name' => $name,
            'value' => $value,
            'hidden_attributes' => $hidden_attributes,
            'display_attributes' => $display_attributes,
            'args' => $args
        ]);
    }

    public function daterangepicker($label, $start_name, $end_name, $start_date = null, $end_date = null, $attributes = [], $args = [])
    {
        $attributes['autocomplete'] = 'off';
        self::create_id($attributes, $label);
        self::find_value($start_name, $start_date);
        self::find_value($end_name, $end_date);

        $variables = compact('label', 'start_name', 'end_name', 'start_date', 'end_date', 'attributes', 'args');
        $variables['form'] = $this;

        return View::factory('form_row/daterangepicker', $variables);
    }

    public function timepicker($label, $name, $value = null, $attributes = [], $args = [])
    {
        $attributes['autocomplete'] = 'off';
        self::add_class($attributes, 'form-timepicker');

        if (empty($args['icon'])) {
            $args['icon'] = '<span class="icon-clock-o fa fa-clock-o"></span>';
        }

        return self::input($label, $name, $value, $attributes, $args);
    }

    /* Create a colour picker*/
    public function colorpicker($label, $name, $value = null, $attributes = [], $args = [])
    {
        self::create_id($attributes, $name);
        self::find_value($name, $value);
        self::add_class($attributes, 'color_picker_input');

        return View::factory('form_row/color_picker')->set([
            'form' => $this,
            'label' => $label,
            'name' => $name,
            'value' => $value,
            'attributes' => $attributes,
            'args' => $args
        ]);
    }

    /* Create a select list */
    public function select($label, $name, $options, $selected = null, $attributes = [], $args = [])
    {
        self::create_id($attributes, $name);
        self::find_value($name, $selected);
        $args['please_select'] = isset($args['please_select']) ? $args['please_select'] : true;

        return View::factory('form_row/select_list')->set([
            'form' => $this,
            'label' => $label,
            'name' => $name,
            'options' => $options,
            'selected' => $selected,
            'attributes' => $attributes,
            'args' => $args
        ]);
    }

    /* Create a multiselect */
    public function multiselect($label, $name, $options, $selected = [], $attributes = [], $args = [])
    {
        self::find_value($name, $selected);
        $attributes['multiple'] = 'multiple';
        $args['please_select'] = false;

        if (!isset($args['multiselect_options'])) {
            $args['multiselect_options'] = [
                'includeSelectAllOption' => true,
                'maxHeight' => 460,
                'selectAllText' => __('ALL')
            ];
        }

        return self::select($label, $name, $options, $selected, $attributes, $args);
    }

    public function combobox($label, $name, $options, $selected = null, $attributes = [], $args = [])
    {
        self::find_value($name, $selected);
        self::create_id($attributes, $name);
        self::add_class($attributes, 'ib-combobox');

        if (empty($args['data-placeholder']) && $label) {
            $args['data-placeholder'] = 'Please select a '.strtolower($label);
        }

        $args['please_select'] = isset($args['please_select']) ? $args['please_select'] : true;

        return self::select($label, $name, $options, $selected, $attributes, $args);
    }

    public function multiselect_table($label, $name, $options, $selected = [], $attributes = [], $args = [])
    {
        self::create_id($attributes, $name);
        self::add_class($attributes, 'ib-combobox');
        $args['please_select'] = isset($args['please_select']) ? $args['please_select'] : true;
        $args['id_field']      = isset($args['id_field'])      ? $args['id_field']      : 'id';
        $args['title_field']   = isset($args['title_field'])   ? $args['title_field']   : 'title';

        if (empty($attributes['data-placeholder'])) {
            $attributes['data-placeholder'] = empty($args['allow_new']) ? 'Select an item and click add' : 'Select or type an item and click add';
        }

        return View::factory('form_row/multiselect_table')->set([
            'form'           => $this,
            'args'           => $args,
            'attributes'     => $attributes,
            'label'          => $label,
            'name'           => $name,
            'options'        => $options,
            'selected_items' => $selected
        ]);

    }

    public function textarea($label, $name, $body = null, $attributes = [], $double_encode = true)
    {
        self::create_id($attributes, $name);
        self::find_value($name, $body);

        if (empty($attributes['rows'])) {
            $attributes['rows'] = 3;
        }

        return View::factory('form_row/textarea')->set([
            'form' => $this,
            'label' => $label,
            'name' => $name,
            'body' => $body,
            'attributes' => $attributes,
            'double_encode' => $double_encode
        ]);
    }

    public function wysiwyg($label, $name, $body = null, $attributes = [], $double_encode = true)
    {
        self::add_class($attributes, 'ckeditor');

        return self::textarea($label, $name, $body, $attributes, $double_encode);
    }

    public function yes_or_no($label, $name, $checked = null, $attributes = [], $args = [])
    {
        self::create_id($attributes, $name);
        self::find_value($name, $checked);
        $checked = ($checked === null) ? true : (bool) $checked;
        $form = $this;

        return View::factory('form_row/yes_or_no', compact('form', 'label', 'name', 'checked', 'attributes', 'args'));
    }

    public function image_uploader($label, $name, $value = null, $attributes = [], $args = [])
    {
        self::create_id($attributes, $name);
        self::find_value($name, $value);
        $attributes['type'] = 'hidden';
        $attributes['name'] = $name;
        $attributes['value'] = $value;
        $args['name'] = $name;
        $args['browse_directory'] = isset($args['browse_directory']) ? $args['browse_directory'] : 'content';

        return View::factory('form_row/image_uploader')->set([
            'form' => $this,
            'label' => $label,
            'name' => $name,
            'value' => $value,
            'attributes' => $attributes,
            'args' => $args
        ]);
    }

    public function ajax_typeselect($label, $name, $hidden_value = null, $display_value, $hidden_attributes = [], $display_attributes = [], $args = [])
    {
        self::find_value($name, $hidden_value);
        self::create_id($hidden_attributes, $name);
        self::create_id($display_attributes, $name.'-display');
        self::add_class($display_attributes, 'form-ajax_typeselect');
        self::add_class($hidden_attributes, 'form-ajax_typeselect-value');
        $hidden_attributes['type']  = 'hidden';



        if (empty($args['icon'])) {
            $args['icon'] = '<span class="flip-horizontally"><span class="icon_search"></span></span>';
        }

        // Turn off the browser autocomplete
        $display_attributes['autocomplete'] = isset($display_attributes['autocomplete']) ? $display_attributes['autocomplete'] : 'off';

        if ($args['url']) {
            $display_attributes['data-url'] = $args['url'];
        }

        return View::factory('form_row/ajax_typeselect')->set([
            'form'  => $this,
            'label' => $label,
            'name'  => $name,
            'hidden_value'       => $hidden_value,
            'display_value'      => $display_value,
            'hidden_attributes'  => $hidden_attributes,
            'display_attributes' => $display_attributes,
            'args'  => $args
        ]);
    }

    public function action_buttons()
    {
        return View::factory('form_row/action_buttons')
            ->set('form', $this);
    }

    public function modal_action_buttons($args = [])
    {
        $buttons = isset($args['buttons']) ? $args['buttons'] : [];
        return View::factory('form_row/modal_action_buttons')
            ->set('buttons', $buttons)
            ->set('form', $this);
    }
}
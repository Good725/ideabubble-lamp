<?php defined('SYSPATH') or die('No direct script access.');

abstract class Database_Result extends Kohana_Database_Result {


    public function as_options($args = []) {
        $array = $this->as_array();

        $selected = isset($args['selected']) ? $args['selected'] : null;
        $id_column = isset($args['id_column']) ? $args['id_column'] : 'id';
        if (isset($args['name_column'])) {
            $name_column = $args['name_column'];
        } else {
            $name_column = (isset($array[0]) && isset($array[0]->name)) ? 'name' : 'title';
        }

        $options = '';

        if (!isset($args['please_select']) || $args['please_select'] !== false) {
            if (!empty($args['combobox']) && (Request::current()->directory() == 'admin')) {
                $options .= '<option value=""></option>';
            } else {
                $options .= '<option value="">'.htmlspecialchars(__('-- Please select --')).'</option>';
            }
        }


        foreach ($array as $result) {
            $id = is_array($result)   ? $result[$id_column]   : $result->{$id_column};
            $name = is_array($result) ? $result[$name_column] : $result->{$name_column};

            $options .= '<option
                value="'.htmlspecialchars($id).'"'.
                ($selected && $selected == $id ? 'selected="selected"' : '').
            '>'.htmlspecialchars($name).'</option>';
        }

        return $options;
    }
}

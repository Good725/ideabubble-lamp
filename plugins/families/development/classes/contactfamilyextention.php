<?php defined('SYSPATH') or die('No Direct Script Access.');

class ContactFamilyExtention extends  ContactsExtention
{
    protected $cache = array();
    public function menus($array)
    {
        if (!Auth::instance()->has_access('contacts2_index_limited')) {
            $array[] = array('name' => 'Families', 'link' => 'admin/families');
        }
        return $array;
    }

    public function required_js()
    {
        return array(
            URL::get_engine_plugin_assets_base('families') . 'js/families.js'
        );
    }

    public function getData($contact, $request = null)
    {
        if (isset($this->cache['data'])) {
            return $this->cache['data'];
        } else {
            $data = array();
            $family = null;
            if (isset($contact['id'])) {
                $family = Model_Families::get_family_of($contact['id']);
            }
            if ($request && $request->query('family_id')) {
                $family = Model_Families::get_family_of(@$contact['id'], $request->query('family_id'));
            }
            if ($family) {
                $data['family'] = $family;
                $data['family']['notes'] = Model_Notes::search(array('type' => 'Family', 'reference_id' => $family['id']));
            }

            $this->cache['data'] = $data;
            return $data;
        }
    }

    public function saveData($contact_id, $post)
    {
        if (isset($post['family'])) {
            foreach ($post['family'] as $family) {
                if ($family['family'] == '') {
                    $family['family'] = $post['last_name'];
                }
                if (!is_numeric($family['id'])) {
                    $family['id'] = Model_Families::set_family(null, $family['family'], 1, 0, $contact_id);
                }
                if (is_numeric($family['id'])) {
                    Model_Families_Members::add_family_member($family['id'], $contact_id, $family['role']);
                }
            }
        }
    }

    public function getTabs($contact_details)
    {
        return array();
    }

    public function getFieldsets($contact_details)
    {
        return array(
            array('name' => 'family', 'title' => 'Family', 'view' => 'contact_family_field', 'position' => 'first')
        );
    }

    public function is_container()
    {
        return true;
    }

    public function get_container()
    {
        return "edit_family";
    }
}
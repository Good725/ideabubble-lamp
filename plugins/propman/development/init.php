<?php
if (class_exists('Controller_Admin_Searchbar')) {
    class PropmanGlobalSearch
    {
        public function search(&$results, &$count, $query)
        {
            $groups = DB::select('*')
                ->from(Model_Propman::GROUPS_TABLE)
                ->where('name', 'like', '%' . $query . '%')
                ->and_where('deleted', '=', 0)
                ->order_by('name')
                ->execute()
                ->as_array();
            foreach ($groups as $group) {
                $results[] = array(
                    'id' => $group['id'],
                    'category' => 'Group',
                    'label' => $group['name'],
                    'link' => '/admin/propman/edit_group/' . $group['id']
                );
                ++$count;
            }

            $properties = DB::select('*')
                ->from(Model_Propman::PROPERTIES_TABLE)
                ->and_where_open()
                    ->where('name', 'like', '%' . $query . '%')
                    ->or_where('ref_code', 'like', '%' . $query . '%')
                ->and_where_close()
                ->and_where('deleted', '=', 0)
                ->order_by('name')
                ->execute()
                ->as_array();
            foreach ($properties as $property) {
                $results[] = array(
                    'id' => $property['id'],
                    'category' => 'Property',
                    'label' => $property['name'] . ' (' . $property['ref_code'] . ')',
                    'link' => '/admin/propman/edit_property/' . $property['id']
                );
                ++$count;
            }
        }
    }
    Controller_Admin_Searchbar::register_globalsearch(new PropmanGlobalSearch());
}

if (class_exists('Controller_Page')) {
    Controller_Page::addActionAlias('cpayment.html', 'Controller_Frontend_Propman', 'action_custom_payment');
}
?>
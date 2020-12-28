<?php
if (class_exists('Controller_Admin_Searchbar')) {
    class PanelsGlobalSearch
    {
        public function search(&$results, &$count, $query)
        {
            $panels = DB::select('*')
                ->from('plugin_panels')
                ->where('title', 'like', '%' . $query . '%')
                ->and_where('deleted', '=', 0)
                ->order_by('title')
                ->execute()
                ->as_array();
            foreach ($panels as $panel) {
                $results[] = array(
                    'id' => $panel['id'],
                    'category' => 'Panels',
                    'label' => $panel['title'],
                    'link' => '/admin/panels/add_edit_item/' . $panel['id']
                );
                ++$count;
            }
        }
    }
    Controller_Admin_Searchbar::register_globalsearch(new PanelsGlobalSearch());
}
?>
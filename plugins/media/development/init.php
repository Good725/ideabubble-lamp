<?php
if (class_exists('Controller_Admin_Searchbar') AND (Model_Plugin::get_isplugin_enabled_foruser('current', 'media'))) {
    class MediaGlobalSearch
    {
        public function search(&$results, &$count, $query)
        {
            $presets = DB::select('*')
                ->from('plugin_media_shared_media_photo_presets')
                ->where('title', 'like', '%' . $query . '%')
                ->and_where('deleted', '=', 0)
                ->order_by('title')
                ->execute()
                ->as_array();
            foreach ($presets as $i => $preset) {
                if ($i >= 10) {
                    break;
                }
                $results[] = array(
                    'id' => $preset['id'],
                    'category' => 'Media presets',
                    'label' => $preset['title'],
                    'link' => '/admin/presets/'
                );
                ++$count;
            }
			
			$medias = DB::select('*')
                ->from('plugin_media_shared_media')
                ->where('filename', 'like', '%' . $query . '%')
                ->order_by('filename')
                ->execute()
                ->as_array();
            foreach ($medias as $i => $media) {
                if ($i >= 10) {
                    break;
                }
                $results[] = array(
                    'id' => $media['id'],
                    'category' => 'Media files',
                    'label' => $media['filename'],
                    'link' => '/admin/media/'
                );
                ++$count;
            }
        }
    }
    Controller_Admin_Searchbar::register_globalsearch(new MediaGlobalSearch());
}

if (Model_Plugin::is_enabled_for_role('Administrator', 'media') && Model_Plugin::is_enabled_for_role('Administrator', 'messaging')) {
    require_once __DIR__ . '/classes/MediaImapSyncEmailPostProcessor.php';
    Model_Messaging::register_post_processor(new MediaImapSyncEmailPostProcessor());
}


?>
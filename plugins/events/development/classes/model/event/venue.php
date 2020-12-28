<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Event_Venue extends ORM
{
	protected $_table_name = 'plugin_events_venues';

	protected $_has_many = array(
		'events' => array('model' => 'Event')
	);

    public function get_url() {
        return '/venue/'.$this->url;
    }

    /*  Get the image. Specify if you want it to fallback to the placeholder */
    public function get_image($args = array())
    {
        $venue = array('image_media_id' => $this->image_media_id);
        return self::static_get_image($venue, $args);
    }

    /* Same as the previous function, but can be used, if the venue has not been loaded as an object. */
    public static function static_get_image($venue, $args)
    {
        $placeholder = isset($args['placeholder']) ? $args['placeholder'] : false;
        $image_path = '';

        if ($venue['image_media_id']) {
            $image_path = Model_Media::get_path_to_id($venue['image_media_id']);
        }

        if (!$image_path && $placeholder) {
            $image_path = Model_Media::get_image_path('no_image_available.png', 'events');
        }

        return $image_path;
    }

    public function get_social_media()
    {
        return self::static_get_social_media($this->as_array());
    }

    public static function static_get_social_media($venue)
    {
        $return = array();

        if (trim($venue['twitter_url'])) {
            $return['twitter'] = array(
                'name' => __('Twitter'),
                'id'   => $venue['twitter_url'],
                'url'  => 'http://twitter.com/'.trim($venue['twitter_url']),
            );
        }

        if (trim($venue['facebook_url'])) {
            $return['facebook'] = array(
                'name' => __('Facebook'),
                'id'   => $venue['facebook_url'],
                'url'  => 'http://facebook.com/'.trim($venue['facebook_url']),
            );
        }

        if (trim($venue['instagram_url'])) {
            $return['instagram'] = array(
                'name' => __('Instagram'),
                'id'   => $venue['instagram_url'],
                'url'  => 'http://instagram.com/'.trim($venue['instagram_url']),
            );
        }

        if (trim($venue['snapchat_url'])) {
            $return['snapchat'] = array(
                'name' => __('Snapchat'),
                'id'   => $venue['snapchat_url'],
                'url'  => 'http://snapchat.com/add/'.trim($venue['snapchat_url']),
            );
        }

        return $return;
    }

    /* Get counties with ongoing events */
    public static function get_active_counties($params = array())
    {
        $counties = array();

        $search = Model_Event::get_for_global_search($params);

        foreach ($search['all_data'] as $result) {
            $data = $result['data'];

            // Get counties that exist within the search results
            if (!empty($result['data']['county']) && !isset($counties[$data['county']])) {
                $counties[$data['county']] = array(
                    'id'   => $data['county_id'],
                    'name' => $data['county']
                );
            }
            ksort($counties);
        }

        return $counties;
    }

}
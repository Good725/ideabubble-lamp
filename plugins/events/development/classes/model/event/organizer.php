<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Event_Organizer extends ORM
{
    protected $_table_name  = Model_Event::TABLE_ORGANIZERS;
    protected $_primary_key = 'contact_id';
    protected $_belongs_to  = array(
        'contact' => array('model' => 'Event_Contact'),
    );

    public function get_url()
    {
        return '/organiser/'.$this->url;
    }

    public function get_name()
    {
        return trim($this->contact->first_name.' '.$this->contact->last_name);
    }

    public function get_profile_image($args = array())
    {
        $use_placeholder  = isset($args['placeholder']) ? $args['placeholder'] : false;
        $image_path       = '';

        if ($this->profile_media_id) {
            $image_path = Model_Media::get_path_to_id($this->profile_media_id);
        }

        // If there is no image and you have allowed the placeholder to be used as a fallback
        if (!$image_path && $use_placeholder) {
            $image_path = Model_Media::get_image_path('no_image_available.png', 'organizers');
        }

        return $image_path;
    }

    /**
     * Get the organiser banner image
     * @param array $args - array of arguments
     *        bool  $args['placeholder'] - if the placeholder should be used if no other image is found
     * @return mixed|null|string
     */
    public function get_banner_image($args = array())
    {
        return self::static_get_banner_image($this->as_array(), $args);
    }

    /* Same as the above function, but can be used when the organizer has not been loaded as an object */
    public static function static_get_banner_image($organizer, $args = array())
    {
        $use_placeholder  = isset($args['placeholder']) ? $args['placeholder'] : false;
        $image_path       = '';

        // Get the url to the banner image, if one
        if ($organizer['banner_media_id']) {
            $image_path = Model_Media::get_path_to_id($organizer['banner_media_id']);
        }

        // If there is no banner or profile image and you have allowed the placeholder to be used as a fallback
        if (!$image_path && $use_placeholder) {
            $image_path = Model_Media::get_image_path('no_image_available.png',  'events');
        }

        return $image_path;
    }
    
    public function get_social_media()
    {
        return self::static_get_social_media($this->as_array());        
    }

    public static function static_get_social_media($organizer)
    {
        $return = array();

        if (trim($organizer['twitter'])) {
            $return['twitter'] = array(
                'name' => __('Twitter'),
                'id'   => $organizer['twitter'],
                'url'  => 'http://twitter.com/'.trim($organizer['twitter']),
            );
        }

        if (trim($organizer['facebook'])) {
            $return['facebook'] = array(
                'name' => __('Facebook'),
                'id'   => $organizer['facebook'],
                'url'  => 'http://facebook.com/'.trim($organizer['facebook']),
            );
        }

        if (trim($organizer['instagram'])) {
            $return['instagram'] = array(
                'name' => __('Instagram'),
                'id'   => $organizer['instagram'],
                'url'  => 'http://instagram.com/'.trim($organizer['instagram']),
            );
        }

        if (trim($organizer['snapchat'])) {
            $return['snapchat'] = array(
                'name' => __('Snapchat'),
                'id'   => $organizer['snapchat'],
                'url'  => 'http://snapchat.com/add/'.trim($organizer['snapchat']),
            );
        }

        return $return;
    }

}
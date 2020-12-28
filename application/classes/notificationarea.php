<?php
/**
 * Purpose: Notification area representation (header menu right side).
 * Rendered in cms.php and shown in header.php.
 */
 
class NotificationArea {
    private static $notification_area;
    private $registered_notifications = array();

    /**
     * return existing Notification object or create new one
     */
    public static function factory() {
        return self::$notification_area ? self::$notification_area : self::$notification_area = new NotificationArea();
    }

    public function register_notification($url, $icon, $cnt, $txt='') {
        $this->registered_notifications[] = array('url'=>$url,'icon'=>$icon, 'cnt'=>$cnt, 'txt'=>$txt);
    }

    /**
     * function should be called by header renderer
     */
	public function generate_links($current_controller = NULL)
	{
		if ($current_controller)
		{
			$current_controller = strtolower($current_controller);
		}

		$links = array();

		foreach ($this->registered_notifications as $notification)
		{
            if (!$notification['cnt']) {
                $number_span='';
            } else {
                $number_span='<span class="user_tools_notification_amount">'.$notification['cnt'].'</span>';
            }

			$links[] .= '<li><a href="/admin/'.$notification['url'].'">Todos'.$number_span.__($notification['txt']).'</a></li>';
		}
		return $links;
	}

}

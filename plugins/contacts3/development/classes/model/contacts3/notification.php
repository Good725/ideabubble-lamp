<?php defined('SYSPATH') or die('No direct script access.');

class Model_Contacts3_Notification extends ORM
{
    protected $_table_name     = 'plugin_contacts3_contact_has_notifications';
    protected $_deleted_column = 'delete';
    protected $_primary_key    = 'id';

    protected $_has_many = [
        'contacts' => ['model' => 'Contacts3_Contact', 'foreign_key' => 'residence'],
        ];


    protected $_belongs_to = [
        'contacts_notification_group' => ['model' => 'Contacts3_NotificationGroup', 'foreign_key' => 'group_id']
    ];

    public static function parse_phone($number)
    {
        static $dial_codes = null;
        if ($dial_codes == null) {
            $dial_codes = array_unique(array_column(Model_Country::$countries, 'dial_code'));
            usort($dial_codes, function($c1, $c2){
                $l1 = strlen($c1);
                $l2 = strlen($c2);
                if ($l1 < $l2) {
                    return 1;
                } else if ($l1 > $l2) {
                    return -1;
                } else {
                    return 0;
                }
            });
        }
        $number = preg_replace('/[^0-9]/', '', $number);
        $area_code_length = 3;
        if ($number[0] == '0') { // national code, remove 0,
            $country_code = Settings::instance()->get('twilio_default_country_code');
            $country_code = str_replace('+', '', trim($country_code));
            $area_code = substr($number, 0, $area_code_length);
            $phone = substr($number, $area_code_length);
        } else {
            if ($number[0] == '0' && $number[1] == '0') {//international code, remove 00
                $number = substr($number, 2);
            }
            if (preg_match('/^(' . implode('|', $dial_codes) . ')(\d+)$/', $number, $ccmatch)) {
                $country_code = $ccmatch[1];
                if ($country_code == '353') {
                    $area_code_length = 2;
                }
                $area_code = '0' . substr($ccmatch[2], 0, $area_code_length);
                $phone = substr($ccmatch[2], $area_code_length);
            } else {
                $country_code = '';
                $area_code = '';
                $phone = $number;
            }
        }
        return array(
            'country_code' => $country_code,
            'area_code' => $area_code,
            'phone' => $phone
        );
    }
}

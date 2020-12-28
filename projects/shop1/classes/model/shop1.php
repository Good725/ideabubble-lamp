<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_Shop1 extends Model
{
    private function createDateRangeArray($strDateFrom, $strDateTo)
    {
        $aryRange = array();
        $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
        $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));
        if ($iDateTo >= $iDateFrom) {
            array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry
            while ($iDateFrom < $iDateTo) {
                $iDateFrom += 86400; // add 24 hours
                array_push($aryRange, date('Y-m-d', $iDateFrom));
            }
        }
        return $aryRange;
    }

    public function calculatedeliverydate()
    {
        $todaydate = date("Y-m-d");
        $todaytime = date('H');
        $datelist = '';
        $earliestdelivery = date("Y-m-d", strtotime(date("Y-m-d", strtotime($todaydate)) . " +1 day")); //default delivery date is tomorrow
        // if it is after 12:00 midday push delivery out 2 days
        if ($todaytime >= 12) {
            $earliestdelivery = date("Y-m-d", strtotime(date("Y-m-d", strtotime($todaydate)) . " +2 day"));
        }
        $enddate = date("Y-m-d", strtotime(date("Y-m-d", strtotime($todaydate)) . " +21 day"));

        $choosedates = $this->createDateRangeArray($earliestdelivery, $enddate);

        foreach ($choosedates as $value) {
            // Get the day of the week and only allow deliveries on Tue - Friday
            if ((date("w", strtotime($value)) >= 2) && (date("w", strtotime($value)) <= 5)) {
                $thisdatestring = date("D j M", strtotime($value));
                $datelist .= '<option value="' . $value . '">' . $thisdatestring . '</option>\n';
            }
        }
        return $datelist;
    }

    public static function get_stores($postage){
        $model = new Model_Shop1();

        $stores = array();

        foreach ($postage as $place) {
            if(  $model->get_postage_rate($place['id']) == 0 ){
                $stores[] = $place;
            }
        }

        return $stores;
    }

    public static function get_locations($postage){
        $model = new Model_Shop1();

        $stores = array();

        foreach ($postage as $place) {
            if(  $model->get_postage_rate($place['id']) > 0 ){
                $stores[] = $place;
            }
        }

        return $stores;
    }

    private function get_postage_rate($zone_id){
        try{
            $q = DB::SELECT('price')
                ->from('plugin_products_postage_rate')
                ->where('zone_id', '=', $zone_id)
                ->execute()
                ->as_array();
            return (float) $q[0]['price'];

        }
        catch(Exception $e){
            IbHelpers::die_r('Database error, please try again later');
        }
    }
}
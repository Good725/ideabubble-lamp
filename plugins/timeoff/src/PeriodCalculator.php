<?php


namespace Ideabubble\Timeoff;

use DateTime;
use DateInterval;

class PeriodCalculator
{
    
    public function calculate($startDate, $endDate, $departmentId, $type = null)
    {
        if ($type == 'lieu') {
            return (strtotime($endDate) - strtotime($startDate)) / 60;
        }

        $minutes = 0;
        $dt = new DateTime($startDate);
        $endDate = new DateTime($endDate);
        if ($dt->format('Y-m-d') == $endDate->format('Y-m-d')) {
            $diff = $endDate->diff($dt);
            $minutes = 60*$diff->h + $diff->i;
            $dminutes = $this->minutesForDate($dt, $departmentId);
            if ($dminutes < $minutes) {
                $minutes = $dminutes;
            }
        } else {
            while ($dt < $endDate) {
                $minutes += $this->minutesForDate($dt, $departmentId);
                $dt->add(new DateInterval('P1D'));
                if ($dt->format('Y-m-d') == $endDate->format('Y-m-d')) {
                    break;
                }
            }
        }
        return $minutes;
    }

    public function format_duration($startDate, $endDate, $departmentId)
    {
        $minutes = 0;
        $dt = new DateTime($startDate);
        $endDate = new DateTime($endDate);
        if ($dt->format('Y-m-d') == $endDate->format('Y-m-d')) {
            $diff = $endDate->diff($dt);
            $minutes = 60*$diff->h + $diff->i;
            $dminutes = $this->minutesForDate($dt, $departmentId);
            if ($dminutes < $minutes) {
                $minutes = $dminutes;
            }
        } else {
            while ($dt < $endDate) {
                $minutes += $this->minutesForDate($dt, $departmentId);
                $dt->add(new DateInterval('P1D'));
                if ($dt->format('Y-m-d') == $endDate->format('Y-m-d')) {
                    break;
                }
            }
        }

        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
        return $hours . 'h ' . $minutes . 'm';
    }

    public function format_minutes($minutes)
    {
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
        return $hours . 'h ' . $minutes . 'm';
    }
    
    private function minutesForDate(DateTime $dt, $departmentId, $type = null)
    {
        static $config_cache = array();

        if ($type == 'lieu') {
            return 24 * 60;
        }

        if (!isset($department_cache[$departmentId])) {
            $department = new \Model_Contacts3_Contact($departmentId);
            $organizationConfig = $department->parents->find_undeleted()->get_timeoff_hours();
            $config_cache[$departmentId] = $organizationConfig;
        } else {
            $organizationConfig = $config_cache[$departmentId];
        }

        if (isset($organizationConfig[strtolower($dt->format('l')).'_hours'])) {
            return \IbHelpers::time_to_hours($organizationConfig[strtolower($dt->format('l')).'_hours']['value']) * 60;
        }

        $config = [
            null, // sunday
            ['09:00', '18:00', 7.5*60],
            ['09:00', '18:00', 7.5*60],
            ['09:00', '18:00', 7.5*60],
            ['09:00', '18:00', 7.5*60],
            ['09:00', '18:00', 7.5*60],
            null //saturday
        ];
        $dow = intval($dt->format('w'));
        if ($config[$dow] !== null) {
            return $config[$dow][2];
        }
        
    }
}
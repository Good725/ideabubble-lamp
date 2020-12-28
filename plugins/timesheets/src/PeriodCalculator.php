<?php


namespace Ideabubble\Timesheets;

use DateTime;
use DateInterval;

class PeriodCalculator
{
    
    public function calculate($startDate, $endDate)
    {
        $minutes = 0;
        $dt = new DateTime($startDate);
        $endDate = new DateTime($endDate);
        if ($dt->format('Y-m-d') == $endDate->format('Y-m-d')) {
            $diff = $endDate->diff($dt);
            $minutes = 60*$diff->h + $diff->m;
        } else {
            while ($dt < $endDate) {
                $minutes += $this->minutesForDate($dt);
                $dt->add(new DateInterval('P1D'));
            }
        }
        return $minutes;
    }
    
    private function minutesForDate(DateTime $dt)
    {
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
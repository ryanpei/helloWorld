<?php

class Util_Time
{
	/**
	 * ...
	 *
	 * @param	mixed	$now
	 * @return	numeric
	 */
    public static function getToday($now = null)
	{
        $time = self::_time($now ?: time());
        return strtotime(date("Y-m-d", $time));
    }

    /**
     *
     * @param  mixed $now
     * @return numeric
     */
    public static function getNextBusinessDay($now = null)
    {
        $time = self::_time($now ?: time());

        if (date('D', $time) == 'Fri' || date('D', $time) == 'Sat') {
            return strtotime('next Monday 12:00 AM', $time);
        } else {
            return strtotime('tomorrow 12:00 AM', $time);
        }
    }

    /**
     * Returns the difference in seconds between 2 provided timestamps
     * @todo  This should be moved into a helper method when we start to port Util Methods
     *
     * @param  $time
     * @return int
     */
    public function getTimeDifference($time1, $time2)
    {
        if ($time1 > $time2) {
            $timeDifference = ($time2 - $time1) * -1;
        } elseif ($time1 < $time2) {
            $timeDifference = $time2 - $time1;
        } else {
            return false;
        }

        return $timeDifference;
    }

    /**
     * Returns a formatted difference in time as a string (must provide time in seconds)
     * @todo   This should be moved into a helper method when we start to port Util Methods
     *
     * @param  $timeInSeconds
     * @return string
     */
    public function formatDisplayTime($timeInSeconds)
    {
        $timeInMinutes = floor($timeInSeconds / 60);
        $timeInHours   = floor($timeInSeconds / 3600);
        $timeInDays    = floor($timeInSeconds / 86400);

        if ($timeInMinutes < 60) {
            $timeString = ($timeInMinutes == 1 ? $timeInMinutes . ' MINUTE' : $timeInMinutes . ' MINUTES');
        } elseif ($timeInMinutes < 1440) {
            $timeString = ($timeInHours > 1 ? $timeInHours . ' HOURS' : $timeInHours . ' HOUR');
        } else {
            $timeString = ($timeInDays == 1 ? $timeInDays . ' DAY' : $timeInDays . ' DAYS');
        }

        return $timeString;
    }

    /**
     * ...
     *
     * @param	mixed	$time
     * @return	integer
     */
    public static function getDayStart($time = null)
    {
        $time = self::_time($time ?: time());
        return strtotime(date("Y-m-d", $time));
    }

    // can always -1 for getDayEnd
    public static function getNextDayStart($time = null)
    {
        $time = self::_time($time ?: time());
        // +1 day -> normalize that to "YYY-mm-dd" -> convert back to ts
        return strtotime(date("Y-m-d", strtotime('+1 day', $time)));
    }

	/**
	 *
	 * @param	mixed	$now
	 * @param	string	$day
	 * @return	numeric
	 */
    static function getWeekStart($now = null, $day = 'Sun')
	{
        $week_start = self::getToday($now);
        while (date('D', $week_start) != $day) {
            $week_start = strtotime('-1 day', $week_start);
        }
        return $week_start;
    }

	/**
	 * ...
	 *
	 * @param	mixed	$now
	 * @return	numeric
	 */
    static function getMonthStart($now = null)
	{
        $time = self::_time($now ?: time());
        return strtotime(date("Y-m-01", $time));
    }

	/**
	 * ...
	 *
	 * @param	mixed	$now
	 * @return	numeric
	 */
    public static function roundHours($now = null)
	{
        $time = $now ?: time();
        return strtotime(date('Y-m-d H:00:00', $time));
    }

	/**
	 * ...
	 *
	 * @param	mixed	$now
	 * @return	numeric
	 */
    public static function roundMinutes($now = null, $min = 1)
	{
        $time = $now ?: time();
        $m = floor(date('i', $time) / $min) * $min;
        return strtotime(date("Y-m-d H:{$m}:00", $time));
    }

    /**
     * Returns an array containing strftime style times across a time range
     *
     * @param  string $strftime time format to fill array with
     * @param  int start timestamp
     * @param  int end timestamp
     * @param  string increment size in style, parseable by strtotime
     * @return array of times within the range
     */
    public static function getFormattedDateRange($format, $start, $end, $increment)
    {
        $rows = array();
        for ($time = $start; $time < $end; $time = strtotime($increment, $time)) {
            $rows[] = strftime($format, $time);
        }

        return $rows;
    }

//** Private

	/**
	 * Converts input to unix timestamp
	 *
	 * @param	mixed	$obj
	 * @return	numeric|false
	 */
    private static function _time($obj = null)
	{
        if (! $obj) {
            return time();
        } else if ($obj instanceof MongoDate) {
            return $obj->sec;
		} else if (is_integer($obj)) {
            return $obj;
		} else {
            return strtotime((string) $obj);
		}
    }
}

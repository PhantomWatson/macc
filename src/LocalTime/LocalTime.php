<?php
namespace App\LocalTime;

use App\View\AppView;
use Cake\Core\Configure;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\View\Helper\TimeHelper;
use DateTime;

class LocalTime
{
    /**
     * Returns the local timezone equivalent of the provided UTC time
     *
     * @param DateTime|FrozenTime|FrozenDate $dateTime
     * @param string $format i18nFormat() compatible formatting string
     * @return string
     */
    public static function get($dateTime, $format = 'MMMM d, yyyy')
    {
        return (new TimeHelper(new AppView()))
            ->format(
                $dateTime,
                $format,
                null,
                Configure::read('localTimezone')
            );
    }

    /**
     * Returns the provided UTC time as a local-timezone date string
     *
     * @param DateTime|FrozenTime|FrozenDate $dateTime
     * @return string
     */
    public static function getDate($dateTime)
    {
        return self::get($dateTime, 'MMMM d, yyyy');
    }

    /**
     * Returns the provided UTC time as a local-timezone "date, time" string
     *
     * @param DateTime|FrozenTime|FrozenDate $dateTime
     * @return string
     */
    public static function getDateTime($dateTime)
    {
        $retval = self::get($dateTime, 'MMM d, yyyy h:mma');

        // Make "AM" and "PM" lowercase
        $retval = str_replace(['AM', 'PM'], ['am', 'pm'], $retval);

        return $retval;
    }
}

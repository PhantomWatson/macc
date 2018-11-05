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
    public static function get($dateTime, $format = 'MMMM d, YYYY')
    {
        return (new TimeHelper(new AppView()))
            ->format(
                $dateTime,
                $format,
                null,
                Configure::read('localTimezone')
            );
    }
}

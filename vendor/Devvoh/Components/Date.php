<?php
/**
 * @package     Devvoh
 * @subpackage  Components
 * @subpackage  Date
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Date {
    use \Devvoh\Components\Traits\GetClassName;

    protected $timezone = null;

    public function getTimezone() {
        return $this->timezone;
    }

    public function setTimezone($timezone) {
        if (!$timezone instanceof \DateTimeZone) {
            $timezone = new \DateTimeZone($timezone);
        }
        $this->timezone = $timezone;
        return $this;
    }

    public function getDateTimeTZ($date) {
        if (!$date instanceof \DateTime) {
            $date = new \DateTime($date);
        }
        $date->setTimezone($this->getTimezone());
        return $date;
    }

    public function getDateTimeFormatTZ($date, $format = 'd-m-Y H:i:s') {
        return $this->getDateTimeTZ($date)->format($format);
    }

    public function getTimeZones() {
        $zones = \DateTimeZone::listIdentifiers();
        $timezones = array();
        foreach ($zones as $zone) {
            $timezone = new \DateTimeZone($zone);
            $gmt = ($timezone->getOffset(new \DateTime()) / 3600);

            $hours = floor($gmt);
            $minutes = 60 * (abs($gmt) - abs($hours));

            $offset = $hours . ':' . ($minutes > 0 ? $minutes : '00');

            if ($offset >= 0) {
                $offset = '+' . $offset;
            }

            $timezones[$zone] = str_replace('/', ': ', $zone) . ' (UTC/GMT ' . $offset .')';
        }
        return $timezones;
    }

}
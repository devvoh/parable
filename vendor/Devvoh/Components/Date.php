<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Date {

    /** @var null|\DateTimeZone */
    protected $timezone = null;

    /**
     * Set the timezone in string or DateTimeZone format
     *
     * @param \DateTimeZone|string $timezone
     * @return $this
     */
    public function setTimezone($timezone) {
        if (!$timezone instanceof \DateTimeZone) {
            $timezone = new \DateTimeZone($timezone);
        }
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * Return the currently set timezone
     *
     * @return \DateTimeZone|null
     */
    public function getTimezone() {
        return $this->timezone;
    }

    /**
     * Get the timezone-corrected date, either now or based on provided date string or DateTime instance
     *
     * @param string|\DateTime $date
     * @return \DateTime
     */
    public function getDateTime($date) {
        if (!$date instanceof \DateTime) {
            $date = new \DateTime($date);
        }
        $date->setTimezone($this->getTimezone());
        return $date;
    }

    /**
     * Return the timezone-corrected date in formatted string value
     *
     * @param null|string $date
     * @param string $format
     * @return string
     */
    public function format($date = null, $format = 'd-m-Y H:i:s') {
        return $this->getDateTime($date)->format($format);
    }

    /**
     * Get a list of all predefined PHP-timezones in select/option array format
     *
     * @return array
     */
    public function getTimeZones() {
        $zones = \DateTimeZone::listIdentifiers();
        $timezones = [];
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
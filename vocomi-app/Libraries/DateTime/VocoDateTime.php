<?php

namespace VocomiApp\Libraries\DateTime;

class VocoDateTime extends \Carbon\Carbon
{
    public function __construct($time = null, $tz = null)
    {
        parent::__construct($time, $tz);
    }

    /**
     *
     * @param \DateTime|string|int $date
     * @param string $default default text to return
     * @return string
     */
    public static function standardDate($date = null, $default = '')
    {
        return static::isEmpty($date) ?
                $default :
                static::formatDate($date)->format(config('vocomi.settings.format.date'));
    }

    /**
     *
     * @param \DateTime|string|int $date
     * @param string $default default text to return
     * @return string
     */
    public static function expandedDate($date = null, $default = '')
    {
        return static::isEmpty($date) ?
                $default :
                static::formatDate($date)->format('M j, Y');
    }

    /**
     *
     * @param \DateTime|string|int $time
     * @param string $default default text to return
     * @return string
     */
    public static function standardTime($time = null, $default = '')
    {
        return static::isEmpty($time) ?
                $default :
                static::formatDate($time)->format(config('vocomi.settings.format.time'));
    }

    /**
     *
     * @param \DateTime|string|int $datetime
     * @param string $default default text to return
     * @return string
     */
    public static function standardDateTime($datetime = null, $default = '')
    {
        return static::isEmpty($datetime) ?
                $default :
                static::formatDate($datetime)->format(config('vocomi.settings.format.datetime'));
    }

    /**
     *
     * @param \DateTime|string|int $datetime
     * @param string $default default text to return
     * @return string
     */
    public static function expandedDateTime($datetime = null, $default = '')
    {
        return static::isEmpty($datetime) ?
                $default :
                static::formatDate($datetime)->format('M j, Y h:ia');
    }

    public static function calendarToMysqlDate($datetime = null, $default = '')
    {
        return static::isEmpty($datetime) ?
                $default :
                self::createFromFormat(
                        config('vocomi.settings.format.jscalendar.format_php')
                        , $datetime
                )->format('Y-m-d');
    }

    public static function mysqlDateToCalendar($datetime = null, $default = '')
    {
        return static::isEmpty($datetime) ?
                $default :
                static::formatDate($datetime)->format(config('vocomi.settings.format.jscalendar.format_php'));
    }

    /**
     *
     * @param \DateTime|string|int $time
     * @return \static
     */
    private static function formatDate($time)
    {
        return new static($time);
    }

    private static function isEmpty($timestamp)
    {
        // tripple equals is a must here
        return (
                '0000-00-00 00:00:00' === $timestamp ||
                '0000-00-00' === $timestamp ||
                '00:00:00' === $timestamp ||
                '' === $timestamp ||
                null === $timestamp
                );
    }

}

<?php

function voco_date_diff_human($datetime)
{
	return \VocomiApp\Libraries\DateTime\VocoDateTime::parse($datetime)->diffForHumans();
}

function voco_standard_datetime($datetime = null, $default = '')
{
    return \VocomiApp\Libraries\DateTime\VocoDateTime::standardDateTime($datetime, $default);
}

function voco_standard_date($date = null, $default = '')
{
    return \VocomiApp\Libraries\DateTime\VocoDateTime::standardDate($date, $default);
}

function voco_standard_time($time = null, $default = '')
{
    return \VocomiApp\Libraries\DateTime\VocoDateTime::standardTime($time, $default);
}

function voco_expanded_datetime($datetime = null, $default = '')
{
    return \VocomiApp\Libraries\DateTime\VocoDateTime::expandedDateTime($datetime, $default);
}

function voco_expanded_date($date = null, $default = '')
{
    return \VocomiApp\Libraries\DateTime\VocoDateTime::expandedDate($date, $default);
}

function voco_js_calendar_date($date = null, $default = '')
{
    return \VocomiApp\Libraries\DateTime\VocoDateTime::mysqlDateToCalendar($date, $default);
}

function voco_calendar_to_mysql_date($date = null, $default = '')
{
    return \VocomiApp\Libraries\DateTime\VocoDateTime::calendarToMysqlDate($date, $default);
}

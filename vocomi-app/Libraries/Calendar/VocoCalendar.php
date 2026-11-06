<?php

namespace VocomiApp\Libraries\Calendar;

use VocomiApp\Libraries\Calendar\Date\CalendarDate;

class VocoCalendar
{
    private $year;
    private $month;
    private $dates = [];
    private $first_day = null;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;

        $this->first_day = (new CalendarDate());
        $this->first_day->setDate($this->year, $this->month, 1);

        $this->prepareCalendar();
    }

    /**
     *
     * @param int $day
     * @return CalendarDate
     */
    public function getDate($day)
    {
        return $this->dates['day' . (int) $day];
    }

    public function render()
    {
        $html = '<table class="table table-bordered voco-calendar">';
        $html .= '<thead><tr>'
                . '<th colspan="7" class="text-center">'
                // format 'F' => January through December
                . $this->first_day->format('F') . ' ' . $this->year
                . '</th>';
        $html .= '</tr></thead>';

        $weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        /* first "tbody" */
        $html .= '<tbody><tr>';
        foreach ($weekDays as $weekDay) {
            $html .= '<td class="text-center">' . $weekDay . '</td>';
        }
        $html .= '</tr></tbody>';

        /* second "tbody" */
        $html .= '<tbody>';
        foreach (array_chunk($this->dates, 7) as $dates) {
            $html .= '<tr>';
            foreach ($dates as $date) {
                $html .= $date->render();
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }

    public function prepareCalendar()
    {
        // Number of days in the given month (28 through 31)
        $days_in_month = $this->first_day->format('t');

        $last_day = (new CalendarDate())->setDate($this->year, $this->month, $days_in_month);

        // format 'w' => 0 (for Sunday) through 6 (for Saturday)
        $blank = $this->first_day->format('w');

        // initial blank cells
        for ($i = $blank; $i > 0; $i--) {
            // $html .= '<td></td>';

            $date = clone $this->first_day;
            $date->modify('-' . $i . ' days');
            $date->setInactive();

            $this->dates['prev' . $i] = $date;
        }

        $date_temp = clone $this->first_day;

        while ($date_temp <= $last_day) {
            $date = clone $date_temp;

            $this->dates['day' . $date->format('j')] = $date;

            $date_temp->modify('+1 day');
        }

        // trailing blank cells
        for ($i = 0; ($i + $blank + $days_in_month) % 7 != 0; $i++) {
            // $html .= '<td></td>';

            $date = clone $date_temp;

            $this->dates['next' . ($i + 1)] = $date;

            $date_temp->modify('+1 day');
        }
    }

}

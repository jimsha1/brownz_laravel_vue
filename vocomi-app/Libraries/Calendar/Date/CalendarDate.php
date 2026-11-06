<?php

namespace VocomiApp\Libraries\Calendar\Date;

class CalendarDate extends \DateTime
{
    private $events = [];
    private $inactive = false;

    public function addEventClass($event_class)
    {
        $this->events['class'][] = $event_class;

        return $this;
    }

    public function addEventText($event_text)
    {
        $this->events['text'][] = '<span class="vcal-event-text">' . $event_text . '</span>';

        return $this;
    }

    public function addEventActionHtml($event_action_html)
    {
        $this->events['action_html'][] = '<span class="vcal-action">' . $event_action_html . '</span>';

        return $this;
    }

    public function setInactive()
    {
        $this->inactive = true;

        return $this;
    }

    public function hasClass()
    {
        return isset($this->events['class']);
    }

    public function hasEvents()
    {
        return (isset($this->events['text']) || $this->events['action_html']);
    }

    public function render()
    {
        return '<td' . $this->getAttributes() . '>'
                . '<span class="date-lg">' . $this->format('j') . '</span>'
                . ' ' . $this->getEventsText()
                . ' ' . $this->getEventsHtml()
                . '</td>';
    }

    private function getAttributes()
    {
        $this->events['class'][] = 'vcal-day';

        if ($this->inactive === true) {
            $this->events['class'][] = 'vcal-day-inactive';
        }

        return ' data-date="' . $this->format('Y-m-d') . '"'
                . ' class="' . implode(' ', $this->events['class']) . '"';
    }

    private function getEventsText()
    {
        if ($this->inactive === true) {
            return '';
        }

        return isset($this->events['text']) ? implode('', $this->events['text']) : '';
    }

    private function getEventsHtml()
    {
        if ($this->inactive === true) {
            return '';
        }

        return isset($this->events['action_html']) ? implode('', $this->events['action_html']) : '';
    }

}

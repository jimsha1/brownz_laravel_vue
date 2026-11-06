<?php

namespace VocomiApp\Helpers;

use Session;

class VocoFlashHelper
{
    private $key = 'voco_flash_data';

    public function success($message)
    {
        return $this->flash($message, 'success');
    }

    public function info($message)
    {
        return $this->flash($message, 'info');
    }

    public function warning($message)
    {
        return $this->flash($message, 'warning');
    }

    public function danger($message)
    {
        return $this->flash($message, 'danger');
    }

    public function error($message)
    {
        return $this->flash($message, 'danger');
    }

    public function flash($message, $type)
    {
        Session::flash($this->key, array_merge(
                        (array) Session::get($this->key)
                        , array(voco_alert()->type(strip_tags($message), $type))
        ));
    }

    public function get()
    {
        return Session::get($this->key);
    }

    public function getAsString()
    {
        return implode(PHP_EOL, (array) $this->get());
    }
}

<?php

namespace VocomiApp\Helpers;

class VocoAlertHelper
{
    public function success($message)
    {
        return $this->type($message, 'success');
    }

    public function info($message)
    {
        return $this->type($message, 'info');
    }

    public function warning($message)
    {
        return $this->type($message, 'warning');
    }

    public function danger($message)
    {
        return $this->type($message, 'danger');
    }

    public function error($message)
    {
        return $this->type($message, 'danger');
    }

    public function type($message, $type)
    {
        // append full stop, if message does not end with punctuation :)
        $message = trim($message);
        if (preg_match('/^\p{L}+$/u', mb_substr($message, -1))) {
            $message .= '.';
        }

        return '<div class="alert alert-dismissable alert-' . $type . '">'
                . '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">'
                . '&times;'
                . '</button>'
                . trim($message)
                . '</div>';
    }
}

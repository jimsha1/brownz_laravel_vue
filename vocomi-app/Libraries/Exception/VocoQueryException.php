<?php

namespace VocomiApp\Libraries\Exception;

class VocoQueryException extends \Exception
{
    /**
     * Construct the exception
     *
     * @param  string $message
     * @param  int $code
     * @param  \Throwable $previous
     * @return void
     */
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        if (empty($message) && $code > 0) {
            $message = $this->getMessageFromCode($code);
        }

        parent::__construct($message, $code, $previous);
    }

    private function getMessageFromCode($errorCode)
    {
        switch ($errorCode) {
            case 0: return null;

            default: return 'Service not available';
        }
    }
}

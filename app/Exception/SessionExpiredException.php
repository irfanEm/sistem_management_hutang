<?php

namespace IRFANM\SIMAHU\Exception;

class SessionExpiredException extends \RuntimeException
{
    public function __construct(string $message = "Sesi telah kadaluarsa", int $code = 401)
    {
        parent::__construct($message, $code);
    }
}
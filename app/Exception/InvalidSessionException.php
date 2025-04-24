<?php

namespace IRFANM\SIMAHU\Exception;

class InvalidSessionException extends \RuntimeException
{
    public function __construct(string $message = "Sesi tidak valid", int $code = 401)
    {
        parent::__construct($message, $code);
    }
}

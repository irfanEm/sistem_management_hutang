<?php

namespace IRFANM\SIMAHU\Exception;

class DataNotFoundException extends \RuntimeException
{
    public function __construct(string $message = "Data tidak ditemukan", int $code = 404)
    {
        parent::__construct($message, $code);
    }
}

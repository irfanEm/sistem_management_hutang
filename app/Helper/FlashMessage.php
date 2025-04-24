<?php

namespace IRFANM\SIMAHU\Helper;

class FlashMessage
{
    public static function setMessage(array $data = null)
    {
        session_start();
        $_SESSION['flash_message'] = $data;
    }
}

<?php

namespace IRFANM\SIMAHU\Model;

use IRFANM\SIMAHU\Domain\Session;

class SessionResponse
{
    public Session $session;
    
    public function __construct(Session $session)
    {
        $this->session = $session;
    }
}

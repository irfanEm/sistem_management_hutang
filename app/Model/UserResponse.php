<?php

namespace IRFANM\SIMAHU\Model;

use IRFANM\SIMAHU\Domain\User;

class UserResponse
{
    public User $user;
    
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
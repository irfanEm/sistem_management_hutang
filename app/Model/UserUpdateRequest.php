<?php

namespace IRFANM\SIASHAF\Model;

class UserUpdateRequest
{
    public string $user_id;
    public ?string $name = null;
    public ?string $username = null;
    public ?string $role = null;
}

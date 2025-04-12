<?php

namespace IRFANM\SIASHAF\Domain;

class User
{
    public string $user_id;
    public string $name;
    public string $username;
    public string $password;
    public string $role;
    public string $created_at;
    public string $updated_at;
    public ?string $deleted_at = null;
}

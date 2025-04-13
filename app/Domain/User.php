<?php

namespace IRFANM\SIASHAF\Domain;

class User
{
    public int $id;
    public string $username;
    public string $nama;
    public string $email;
    public string $password;
    public string $role;
    public int $status;
    public ?string $reset_token = null;
    public ?string $reset_expiry = null;
    public string $created_at;
    public string $updated_at;
}

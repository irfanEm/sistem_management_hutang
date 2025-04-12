<?php

namespace IRFANM\SIASHAF\Domain;

class Vip
{
    public string $user_id = "irfan_em27";
    private string $username = "adam_hawa@superadmin.com";
    private string $password = "bismillah";
    public string $role = "superadmin";
    public string $created_at = "1945-08-17 00:00:00";
    public string $updated_at = "1945-08-17 00:00:00";
    public ?string $deleted_at = null;

    public function getVipUsername(): string
    {
        return $this->username;
    }

    public function getVipPassword(): string
    {
        return $this->password;
    }
}

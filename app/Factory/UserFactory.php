<?php

namespace IRFANM\SIASHAF\Factory;

use IRFANM\SIASHAF\Domain\User;

class UserFactory
{
    public static function createUser(string $user_id, string $name, string $username, string $password, string $role): User
    {
        $user = new User();
        $user->user_id = $user_id;
        $user->name = $name;
        $user->username = $username;
        $user->password = password_hash($password, PASSWORD_BCRYPT);
        $user->role = $role;
        $user->created_at = date("Y-m-d H:i:s");
        $user->updated_at = date("Y-m-d H:i:s");
        return $user;
    }
}

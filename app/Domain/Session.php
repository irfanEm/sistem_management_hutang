<?php

namespace IRFANM\SIASHAF\Domain;

class Session {
    public int $id;
    public int $user_id;
    public string $session_token;
    public string $ip_address;
    public string $user_agent;
    public ?string $login_time = null;
    public ?string $last_activity = null;
    public string $expiry_time;
}

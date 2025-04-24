<?php

namespace IRFANM\SIMAHU\Middleware;

use IRFANM\SIMAHU\App\View;
use IRFANM\SIMAHU\Config\Database;
use IRFANM\SIMAHU\Repository\SessionRepository;
use IRFANM\SIMAHU\Repository\UserRepository;
use IRFANM\SIMAHU\Service\SessionService;

class AlreadyLoginMiddleware implements Middleware
{
    private SessionService $sessionService;

    public function __construct()
    {
        $conn = Database::getConn();
        $sessionRepository = new SessionRepository($conn);
        $userRepository = new UserRepository($conn);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function before()
    {
        $user = $this->sessionService->current();
        if($user != null) {
            View::redirect("/admin/beranda");
        }
    }
}

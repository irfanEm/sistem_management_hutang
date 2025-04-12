<?php

namespace IRFANM\SIASHAF\Middleware;

use IRFANM\SIASHAF\App\View;
use IRFANM\SIASHAF\Config\Database;
use IRFANM\SIASHAF\Repository\SessionRepository;
use IRFANM\SIASHAF\Repository\UserRepository;
use IRFANM\SIASHAF\Service\SessionService;

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

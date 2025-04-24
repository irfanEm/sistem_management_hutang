<?php

namespace IRFANM\SIMAHU\Controller;

use IRFANM\SIMAHU\App\View;
use IRFANM\SIMAHU\Config\Database;
use IRFANM\SIMAHU\Repository\AgenRepository;
use IRFANM\SIMAHU\Service\AgenService;

class AgenController
{
    private AgenService $agenService;

    public function __construct()
    {
        $agenRepository = new AgenRepository(Database::getConn());
        $this->agenService = new AgenService($agenRepository);
    }

    public function index()
    {
        $agens = $this->agenService->getAgen();
        View::render();
    }
}

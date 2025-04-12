<?php

namespace IRFANM\SIASHAF\Controller;

use Dotenv\Dotenv;
use IRFANM\SIASHAF\Config\Database;

class TestController
{
    public function index()
    {
        echo json_encode([
            'status' => 'success',
        ]);
    }

    public function testDotEnvLibrary()
    {
        $dotEnv = Dotenv::createImmutable("../");
        $dotEnv->load();
        $dbTestHost = $_ENV['DB_TEST_HOST'];
        echo $dbTestHost;
    }

    public function testConnDb()
    {
        $testConn = Database::getConn();
        var_dump($testConn);
    }
}

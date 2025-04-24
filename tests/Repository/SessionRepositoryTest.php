<?php

namespace IRFANM\SIMAHU\Tests\Repository;

use IRFANM\SIMAHU\Domain\User;
use PHPUnit\Framework\TestCase;
use IRFANM\SIMAHU\Domain\Session;
use IRFANM\SIMAHU\Config\Database;
use IRFANM\SIMAHU\Repository\SessionRepository;
use IRFANM\SIMAHU\Repository\UserRepository;

class SessionRepositoryTest extends TestCase
{
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    public function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConn());
        $this->sessionRepository = new SessionRepository(Database::getConn());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAllPermanently();

        $user = new User();
        $user->user_id = 'user_dummy00';
        $user->name = 'Nama User';
        $user->username = 'user_dummy00@test.com';
        $user->password = 'password_dummy00';
        $user->role = 'santri';
        $user->created_at = date('Y-m-d H:i:s');
        $user->updated_at = date('Y-m-d H:i:s');
        $this->userRepository->save($user);
    }

    public function testSaveSuccess()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->user_id = "user_dummy00";
        $session->username = "user_name@mail.com";
        $this->sessionRepository->save($session);

        $result = $this->sessionRepository->findById($session->id);
        self::assertNotNull($result);
        self::assertEquals($result->id, $session->id);
        self::assertEquals($result->user_id, "user_dummy00");
    }

    public function testDeleteByIdSuccess()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->user_id = "user_dummy00";
        $session->username = "user_name@mail.com";
        $this->sessionRepository->save($session);

        $result = $this->sessionRepository->deleteById($session->id);
        self::assertTrue($result);
    }

    public function testFindByIdNotFound()
    {
        $result = $this->sessionRepository->findById('notFound');
        self::assertNull($result);
    }

}
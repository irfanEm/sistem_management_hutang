<?php

namespace IRFANM\SIMAHU\Tests\Service;

use IRFANM\SIMAHU\Config\Database;
use IRFANM\SIMAHU\Domain\Session;
use IRFANM\SIMAHU\Domain\User;
use IRFANM\SIMAHU\Domain\Vip;
use IRFANM\SIMAHU\Repository\SessionRepository;
use IRFANM\SIMAHU\Repository\UserRepository;
use IRFANM\SIMAHU\Service\SessionService;
use PHPUnit\Framework\TestCase;

class SessionServiceTest extends TestCase
{
    private SessionRepository $sessionRepo;
    private UserRepository $userRepo;
    private SessionService $sessionService;
    private Vip $vip;

    protected function setUp(): void
    {
        $conn = Database::getConn();
        $this->sessionRepo = new SessionRepository($conn);
        $this->userRepo = new UserRepository($conn);
        $this->sessionService = new SessionService($this->sessionRepo, $this->userRepo);
        $this->vip = new Vip();
        
        $this->sessionRepo->deleteAll();
        $this->userRepo->deleteAllPermanently();
    }

    public function testCreateSession()
    {
        $user = $this->createTestUser();
        
        $session = $this->sessionService->create($user);
        
        self::assertNotNull($session);
        self::assertEquals($user->user_id, $session->user_id);
    }

    public function testDestroySession()
    {
        $user = $this->createTestUser();
        $session = $this->sessionService->create($user);
        
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
        
        $this->sessionService->destroy();
        
        $result = $this->sessionRepo->findById($session->id);
        self::assertNull($result);
    }

    public function testCurrentUser()
    {
        $user = $this->createTestUser();
        $session = $this->sessionService->create($user);
        
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
        
        $currentUser = $this->sessionService->current();
        
        self::assertInstanceOf(User::class, $currentUser);
        self::assertEquals($user->user_id, $currentUser->user_id);
    }

    public function testCurrentVipUser()
    {
        // Setup VIP session
        $vipUser = new User();
        $vipUser->user_id = $this->vip->user_id;
        $vipUser->username = $this->vip->getVipUsername();
        $session = $this->sessionService->create($vipUser);
        
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
        
        $currentUser = $this->sessionService->current();
        
        self::assertEquals($this->vip->name, $currentUser->name);
        self::assertEquals($this->vip->role, $currentUser->role);
    }

    public function testCurrentUserNotFound()
    {
        $_COOKIE[SessionService::$COOKIE_NAME] = 'invalid_session';
        
        $currentUser = $this->sessionService->current();
        
        self::assertNull($currentUser);
    }

    private function createTestUser(): User
    {
        $user = new User();
        $user->user_id = uniqid();
        $user->name = "Irfan M";
        $user->username = 'user_perm@test.com';
        $user->password = password_hash("password", PASSWORD_BCRYPT);
        $user->role = 'user';
        $user->created_at = date("Y-m-d H:i:s");
        $user->updated_at = date("Y-m-d H:i:s");
        $user->deleted_at = null;

        
        $this->userRepo->save($user);
        return $user;
    }
}
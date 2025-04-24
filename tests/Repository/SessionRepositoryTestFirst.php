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
    private UserRepository $userRepository;
    private SessionRepository $sessionRepo;
    private int $testUserId;

    protected function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConn());
        $this->sessionRepo = new SessionRepository(Database::getConn());
        
        $this->userRepository->deleteAllPermanently();
        $this->sessionRepo->deleteAllPermanently();

        $user = $this->createTestUser();
        $this->testUserId = $user->id;
    }

    public function testSaveSuccess()
    {
        $session = $this->createTestSession();
        
        $result = $this->sessionRepo->findByToken($session->session_token);
        
        self::assertNotNull($result);
        self::assertEquals($session->session_token, $result->session_token);
        self::assertEquals($this->testUserId, $result->user_id);
    }

    public function testUpdateSuccess()
    {
        $session = $this->createTestSession();
        
        // Update data
        $session->last_activity = date('Y-m-d H:i:s');
        $session->expiry_time = date('Y-m-d H:i:s', strtotime('+2 hours'));
        
        $this->sessionRepo->update($session);
        
        $updated = $this->sessionRepo->findByToken($session->session_token);
        
        self::assertEquals($session->expiry_time, $updated->expiry_time);
        self::assertNotNull($updated->last_activity);
    }

    public function testFindByTokenNotFound()
    {
        $result = $this->sessionRepo->findByToken('invalid_token');
        self::assertNull($result);
    }

    public function testDeleteSuccess()
    {
        $session = $this->createTestSession();
        
        $this->sessionRepo->delete($session->session_token);
        
        $result = $this->sessionRepo->findByToken($session->session_token);
        self::assertNull($result);
    }

    public function testDeleteExpiredSessions()
    {
        // Create expired session
        $expired = $this->createTestSession(
            expiryTime: date('Y-m-d H:i:s', strtotime('-1 hour'))
        );
        
        // Create active session
        $active = $this->createTestSession(
            sessionToken: 'active_token',
            expiryTime: date('Y-m-d H:i:s', strtotime('+1 hour'))
        );
        
        $this->sessionRepo->deleteExpiredSessions();
        
        self::assertNull($this->sessionRepo->findByToken($expired->session_token));
        // self::assertNotNull($this->sessionRepo->findByToken($active->session_token));
    }

    public function testDeleteAllByUserId()
    {
        // Create 3 sessions for user 1
        $this->createTestSession(sessionToken: 'token1');
        $this->createTestSession(sessionToken: 'token2');
        $this->createTestSession(sessionToken: 'token3');
        $user_id2 = $this->createTestUser('another_user@test.com');
        
        // Create session for other user
        $otherSession = new Session();
        $otherSession->user_id = $user_id2->id;
        $otherSession->session_token = 'other_token';
        $otherSession->ip_address = '127.0.0.1';
        $otherSession->user_agent = 'PHPUnit';
        $otherSession->expiry_time = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $this->sessionRepo->save($otherSession);
        
        $this->sessionRepo->deleteAllByUserId($this->testUserId);
        
        $sessions = $this->sessionRepo->findAll();
        self::assertCount(1, $sessions);
        self::assertEquals('other_token', $sessions[0]->session_token);
    }

    public function testDeleteAllPermanently()
    {
        $this->createTestSession();
        $this->createTestSession(sessionToken: 'token2');
        
        $this->sessionRepo->deleteAllPermanently();
        
        $sessions = $this->sessionRepo->findAll();
        self::assertCount(0, $sessions);
    }

    private function createTestSession(
        string $sessionToken = 'test_token',
        string $expiryTime = '+1 hour'
    ): Session {
        $session = new Session();
        $session->user_id = $this->testUserId;
        $session->session_token = $sessionToken;
        $session->ip_address = '127.0.0.1';
        $session->user_agent = 'PHPUnit';
        $session->expiry_time = date('Y-m-d H:i:s', strtotime($expiryTime));
        
        return $this->sessionRepo->save($session);
    }

    private function createTestUser(string $email = 'user_default@test.com')
    {
        $user = new User();
        $user->username = uniqid();
        $user->nama = 'User Test Default';
        $user->email = $email;
        $user->password = 'password_default';
        $user->role = 'user';
        $user->status = 0;
        $user->reset_token = null;
        $user->reset_expiry = null;
        $user->created_at = date("Y-m-d H:i:s");
        $user->updated_at = date("Y-m-d H:i:s");

        return $this->userRepository->save($user);
    }

}
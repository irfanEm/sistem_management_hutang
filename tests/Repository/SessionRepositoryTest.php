<?php

namespace IRFANM\SIASHAF\Repository;

use IRFANM\SIASHAF\Config\Database;
use IRFANM\SIASHAF\Domain\Session;
use IRFANM\SIASHAF\Domain\User;
use PHPUnit\Framework\TestCase;

class SessionRepositoryTest extends TestCase
{
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConn());
        $this->userRepository = new UserRepository(Database::getConn());
        
        $this->sessionRepository->deleteExpiredSessions();
        $this->userRepository->deleteAllPermanently();
    }

    public function testSaveSuccess()
    {
        // Setup user
        $user = $this->createTestUser();
        
        // Create session
        $session = new Session();
        $session->user_id = $user->id;
        $session->session_token = bin2hex(random_bytes(32));
        $session->ip_address = '127.0.0.1';
        $session->user_agent = 'PHPUnit';
        $session->expiry_time = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $this->sessionRepository->save($session);
        $savedSession = $this->sessionRepository->findByToken($session->session_token);
        
        self::assertEquals($session->session_token, $savedSession->session_token);
        self::assertEquals($user->id, $savedSession->user_id);
        self::assertNotNull($savedSession->id);
    }

    public function testFindByToken()
    {
        // Setup
        $session = $this->createTestSession();
        
        // Test find
        $foundSession = $this->sessionRepository->findByToken($session->session_token);
        
        self::assertNotNull($foundSession);
        self::assertEquals($session->id, $foundSession->id);
    }

    public function testUpdateLastActivity()
    {
        $session = $this->createTestSession();
        $newLastActivity = date('Y-m-d H:i:s');
        
        $session->last_activity = $newLastActivity;
        $updatedSession = $this->sessionRepository->update($session);
        
        self::assertEquals($newLastActivity, $updatedSession->last_activity);
    }

    public function testDeleteSession()
    {
        $session = $this->createTestSession();
        
        $this->sessionRepository->delete($session->session_token);
        $result = $this->sessionRepository->findByToken($session->session_token);
        
        self::assertNull($result);
    }

    public function testDeleteExpiredSessions()
    {
        // Create expired session
        $expiredSession = $this->createTestSession();
        $expiredSession->expiry_time = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $this->sessionRepository->update($expiredSession);
        
        // Delete expired
        $this->sessionRepository->deleteExpiredSessions();
        $result = $this->sessionRepository->findByToken($expiredSession->session_token);
        
        self::assertNull($result);
    }

    private function createTestUser(): User
    {
        $user = new User();
        $user->username = uniqid();
        $user->nama = 'Test User';
        $user->email = 'test@session.com';
        $user->password = 'password';
        $user->role = 'user';
        $user->status = 1;
        $user->created_at = date('Y-m-d H:i:s');
        $user->updated_at = date('Y-m-d H:i:s');
        $this->userRepository->save($user);
        return $this->userRepository->findById($user->username);
    }

    private function createTestSession(): Session
    {
        $user = $this->createTestUser();
        
        $session = new Session();
        $session->user_id = $user->id;
        $session->session_token = bin2hex(random_bytes(32));
        $session->ip_address = '127.0.0.1';
        $session->user_agent = 'PHPUnit';
        $session->expiry_time = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $this->sessionRepository->save($session);
        return $this->sessionRepository->findByToken($session->session_token);
    }
}
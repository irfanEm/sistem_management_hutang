<?php

namespace IRFANM\SIMAHU\Service;

use IRFANM\SIMAHU\Config\Database;
use IRFANM\SIMAHU\Domain\Session;
use IRFANM\SIMAHU\Exception\SessionExpiredException;
use IRFANM\SIMAHU\Exception\InvalidSessionException;
use IRFANM\SIMAHU\Model\SessionResponse;
use IRFANM\SIMAHU\Repository\SessionRepository;

class SessionService
{
    private SessionRepository $sessionRepository;
    private int $sessionLifetimeHours = 2; // Masa aktif sesi dalam jam

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    public function startSession(
        int $userId, 
        string $ipAddress, 
        string $userAgent
    ): SessionResponse {
        try {
            Database::beginTransaction();

            // Generate session token
            $sessionToken = bin2hex(random_bytes(32));
            
            $session = new Session();
            $session->user_id = $userId;
            $session->session_token = $sessionToken;
            $session->ip_address = $ipAddress;
            $session->user_agent = $userAgent;
            $session->login_time = date('Y-m-d H:i:s');
            $session->last_activity = date('Y-m-d H:i:s');
            $session->expiry_time = date(
                'Y-m-d H:i:s', 
                strtotime("+{$this->sessionLifetimeHours} hours")
            );

            $savedSession = $this->sessionRepository->save($session);

            Database::commitTransaction();
            
            return new SessionResponse($savedSession);
        } catch (\Exception $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function refreshSession(string $token): SessionResponse
    {
        try {
            Database::beginTransaction();

            $session = $this->validateSession($token)->session;
            
            // Update last activity dan perpanjang masa aktif
            $session->last_activity = date('Y-m-d H:i:s');
            $session->expiry_time = date(
                'Y-m-d H:i:s', 
                strtotime("+{$this->sessionLifetimeHours} hours")
            );

            $updatedSession = $this->sessionRepository->update($session);

            Database::commitTransaction();
            
            return new SessionResponse($updatedSession);
        } catch (\Exception $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function endSession(string $token): void
    {
        try {
            Database::beginTransaction();
            
            if (!$this->sessionRepository->delete($token)) {
                throw new InvalidSessionException("Gagal menghapus sesi");
            }
            
            Database::commitTransaction();
        } catch (\Exception $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function validateSession(string $token): SessionResponse
    {
        $session = $this->sessionRepository->findByToken($token);

        if (!$session) {
            throw new InvalidSessionException("Sesi tidak valid");
        }

        if (strtotime($session->expiry_time) < time()) {
            $this->sessionRepository->delete($token);
            throw new SessionExpiredException("Sesi telah kadaluarsa");
        }

        return new SessionResponse($session);
    }

    public function cleanupExpiredSessions(): void
    {
        try {
            Database::beginTransaction();
            $this->sessionRepository->deleteExpiredSessions();
            Database::commitTransaction();
        } catch (\Exception $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function endAllSessionsForUser(int $userId): void
    {
        try {
            Database::beginTransaction();
            $this->sessionRepository->deleteAllByUserId($userId);
            Database::commitTransaction();
        } catch (\Exception $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function getSessionDetails(string $token): SessionResponse
    {
        $session = $this->sessionRepository->findByToken($token);
        
        if (!$session) {
            throw new InvalidSessionException("Sesi tidak ditemukan");
        }
        
        return new SessionResponse($session);
    }
}
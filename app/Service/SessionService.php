<?php

namespace IRFANM\SIMAHU\Service;

use IRFANM\SIMAHU\Domain\Vip;
use IRFANM\SIMAHU\Domain\User;
use IRFANM\SIMAHU\Domain\Session;
use IRFANM\SIMAHU\Config\Database;
use IRFANM\SIMAHU\Model\SessionResponse;
use IRFANM\SIMAHU\Repository\UserRepository;
use IRFANM\SIMAHU\Repository\SessionRepository;
use IRFANM\SIMAHU\Exception\InvalidSessionException;
use IRFANM\SIMAHU\Exception\SessionExpiredException;

class SessionService
{
    public static string $COOKIE_NAME = "X-SIMAHU-IEM";
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;
    private Vip $vip;

    public function __construct(SessionRepository $sessionRepository, UserRepository $userRepository)
    {
        $this->sessionRepository = $sessionRepository;
        $this->userRepository = $userRepository;
        $this->vip = new Vip();
    }

    public function create(User $user)
    {
        $session = new Session();
        $session->id = uniqid();
        $session->user_id = $user->user_id;
        $session->username = $user->username;

        $this->sessionRepository->save($session);

        // Simpan cookie dengan keamanan tambahan
        setcookie(self::$COOKIE_NAME, $session->id, [
            'expires' => time() + (3600 * 3),
            'path' => '/',
            'httponly' => true,
            'secure' => true,
        ]);
        return $session;
    }

    public function destroy()
    {
        $session_id = $_COOKIE[self::$COOKIE_NAME] ?? '';
        $this->sessionRepository->deleteById($session_id);
        setcookie(self::$COOKIE_NAME, "", [
            'expires' => time() - 3600,
            'path' => '/',
            'httponly' => true,
            'secure' => true,
        ]);
    }

    public function current(): ?User
    {
        $session_id = $this->getSessionIdFromCookie();
        if ($session_id === '') {
            return null;
        }

        $session = $this->sessionRepository->findById($session_id);
        if ($session === null) {
            return null;
        }

        // Jika user adalah superadmin (VIP)
        if ($session->user_id === $this->vip->user_id) {
            return $this->mapToUser($this->vip);
        }

        return $this->userRepository->findById($session->user_id);
    }

    private function getSessionIdFromCookie(): string
    {
        return $_COOKIE[self::$COOKIE_NAME] ?? '';
    }

    private function mapToUser(Vip $vip): User
    {
        $user = new User();
        foreach (get_object_vars($vip) as $key => $value) {
            if (property_exists($user, $key)) {
                $user->$key = $value;
            }
        }
        return $user;
    }
}
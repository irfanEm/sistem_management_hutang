<?php

namespace IRFANM\SIMAHU\App;

use IRFANM\SIMAHU\Config\Database;
use IRFANM\SIMAHU\Repository\SessionRepository;
use IRFANM\SIMAHU\Repository\UserRepository;
use IRFANM\SIMAHU\Service\SessionService;

class View
{
    private static ?SessionService $sessionService = null;

    public static function init(): void
    {
        // Inisialisasi dependensi secara statis (hanya sekali)
        if (self::$sessionService === null) {
            $sessionRepository = new SessionRepository(Database::getConn());
            $userRepository = new UserRepository(Database::getConn());
            self::$sessionService = new SessionService($sessionRepository, $userRepository);
        }
    }

    public static function render(string $view, mixed $model = null): void
    {
        // Pastikan dependensi telah diinisialisasi
        self::init();

        $current = self::$sessionService->current();
        
        // Render header
        require_once __DIR__ . "/../View/header.php";

        // Jika sesi ada, render navbar
        if ($current !== null) {
            require_once __DIR__ . "/../View/navbar.php";
        }

        // Render view utama
        require_once __DIR__ . "/../View/" . $view . ".php";

        // Render footer
        require_once __DIR__ . "/../View/footer.php";
    }

    public static function redirect(string $url, array $data = []): void
    {
        if (!empty($data)) {
            session_start(); // Mulai session
            $_SESSION['flash_message'] = $data; // Simpan data ke session
            session_write_close(); // Pastikan session tersimpan
        }
    
        header("Location: $url");
        if (getenv("mode") !== "test") {
            exit();
        }
    }
}

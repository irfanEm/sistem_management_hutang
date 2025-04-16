<?php

namespace IRFANM\SIASHAF\Repository;

use IRFANM\SIASHAF\Domain\Session;
use PDO;
use PDOException;

class SessionRepository
{
    private PDO $connection;
    private const TABLE_NAME = 'sessions';

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    private function mapRowToData(array $row): Session
    {
        $session = new Session();
        $session->id = $row['id'];
        $session->user_id = $row['user_id'];
        $session->session_token = $row['session_token'];
        $session->ip_address = $row['ip_address'];
        $session->user_agent = $row['user_agent'];
        $session->login_time = $row['login_time'];
        $session->last_activity = $row['last_activity'];
        $session->expiry_time = $row['expiry_time'];

        return $session;
    }

    public function save(Session $session): Session
    {
        try {
            $statement = $this->connection->prepare("
                INSERT INTO " . self::TABLE_NAME . " 
                (user_id, session_token, ip_address, user_agent, expiry_time) 
                VALUES (?, ?, ?, ?, ?)
            ");

            $statement->execute([
                $session->user_id,
                $session->session_token,
                $session->ip_address,
                $session->user_agent,
                $session->expiry_time,
            ]);

            return $session;
        } catch (PDOException $err) {
            error_log("Error saat menyimpan session: " . $err->getMessage());
            throw $err;
        }
    }

    public function update(Session $session): Session
    {
        try {
            $statement = $this->connection->prepare("
                UPDATE " . self::TABLE_NAME . " 
                SET last_activity = ?, expiry_time = ?
                WHERE session_token = ?
            ");

            $statement->execute([
                $session->last_activity,
                $session->expiry_time,
                $session->session_token,
            ]);

            return $session;
        } catch (PDOException $err) {
            error_log("Error saat memperbarui session: " . $err->getMessage());
            throw $err;
        }
    }

    public function findByToken(string $token): ?Session
    {
        try {
            $statement = $this->connection->prepare("
                SELECT * FROM " . self::TABLE_NAME . " 
                WHERE session_token = ?
            ");
            $statement->execute([$token]);

            if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                return $this->mapRowToData($row);
            }

            return null;
        } catch (PDOException $err) {
            error_log("Error saat mencari session: " . $err->getMessage());
            return null;
        }
    }

    public function delete(string $token): bool
    {
        try {
            $statement = $this->connection->prepare("
                DELETE FROM " . self::TABLE_NAME . " 
                WHERE session_token = ?
            ");
            return $statement->execute([$token]);
        } catch (PDOException $err) {
            error_log("Error saat menghapus session: " . $err->getMessage());
            return false;
        }
    }

    public function deleteExpiredSessions(): bool
    {
        try {
            $statement = $this->connection->prepare("
                DELETE FROM " . self::TABLE_NAME . " 
                WHERE expiry_time < NOW()
            ");
            return $statement->execute();
        } catch (PDOException $err) {
            error_log("Error saat menghapus session expired: " . $err->getMessage());
            return false;
        }
    }

    public function deleteAllByUserId(int $userId): bool
    {
        try {
            $statement = $this->connection->prepare("
                DELETE FROM " . self::TABLE_NAME . " 
                WHERE user_id = ?
            ");
            return $statement->execute([$userId]);
        } catch (PDOException $err) {
            error_log("Error saat menghapus session user: " . $err->getMessage());
            return false;
        }
    }
}
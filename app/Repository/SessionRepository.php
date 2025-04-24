<?php

namespace IRFANM\SIMAHU\Repository;

use IRFANM\SIMAHU\Domain\Session;
use PDO;
use PDOException;

class SessionRepository
{
    private PDO $connection;
    private const TABLE_NAME = 'sessions2';

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Save a new session.
     *
     * @param Session $session
     * @return Session
     * @throws PDOException
     */
    public function save(Session $session): Session
    {
        try {
            $statement = $this->connection->prepare("INSERT INTO ". self::TABLE_NAME . " (id, user_id, username) VALUES (?, ?, ?)");
            $statement->execute([
                $session->id,
                $session->user_id,
                $session->username
            ]);

            return $session;
        } catch (PDOException $err) {
            error_log("Error saat menyimpan session: " . $err->getMessage());
            throw $err; // Lempar ulang jika perlu
        }
    }

    /**
     * Find a session by its ID.
     *
     * @param string $id
     * @return Session|null
     */
    public function findById(string $id): ?Session
    {
        try {
            $statement = $this->connection->prepare("SELECT id, user_id, username FROM ". self::TABLE_NAME . " WHERE id = ?");
            $statement->execute([$id]);
            $statement->setFetchMode(PDO::FETCH_ASSOC);

            if ($row = $statement->fetch()) {
                $session = new Session();
                $session->id = $row['id'];
                $session->user_id = $row['user_id'];
                $session->username = $row['username'];
                return $session;
            }
            return null;
        } catch (PDOException $err) {
            error_log("Error saat mencari session: " . $err->getMessage());
            return null;
        }
    }

    /**
     * Delete a session by its ID.
     *
     * @param string $id
     * @return bool
     */
    public function deleteById(string $id): bool
    {
        try {
            $statement = $this->connection->prepare("DELETE FROM ". self::TABLE_NAME . " WHERE id = ?");
            $statement->execute([$id]);
            return $statement->rowCount() > 0; // True jika ada baris yang terhapus
        } catch (PDOException $err) {
            error_log("Error saat menghapus session: " . $err->getMessage());
            return false;
        }
    }

    public function deleteAll(): bool
    {
        try {
            $statement = $this->connection->prepare("DELETE FROM ". self::TABLE_NAME . "");
            $statement->execute();
            return true;
        } catch (PDOException $err) {
            error_log("Error saat menghapus semua session: " . $err->getMessage());
            return false;
        }
    }

}
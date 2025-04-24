<?php

namespace IRFANM\SIMAHU\Repository;

use IRFANM\SIMAHU\Domain\User;
use PDO;
use PDOException;

class UserRepository
{
    private PDO $connection;
    private const TABLE_NAME = 'users';

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    private function mapRowToData(array $row): User
    {
        $user = new User();
        $user->id = $row['id'];
        $user->username = $row['username'];
        $user->nama = $row['nama'];
        $user->email = $row['email'];
        $user->password = $row['password'];
        $user->role = $row['role'];
        $user->status = $row['status'];
        $user->reset_token = $row['reset_token'];
        $user->reset_expiry = $row['reset_expiry'];
        $user->created_at = $row['created_at'];
        $user->updated_at = $row['updated_at'];

        return $user;
    }

    public function getAll(string $condition = '', array $params = []): array
    {
        try {
            $query = "SELECT * FROM " . self::TABLE_NAME;
            if ($condition) {
                $query .= " $condition";
            }

            $statement = $this->connection->prepare($query);
            $statement->execute($params);

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $err) {
            error_log("Query: $query");
            throw new \Exception("Error saat mengambil data " . self::TABLE_NAME . ": " . $err->getMessage());
        }
    }

    public function save(User $user): User
    {
        try {
            $statement = $this->connection->prepare("
                INSERT INTO users (username, nama, email, password, role, status, reset_token, reset_expiry, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $statement->execute([
                $user->username,
                $user->nama,
                $user->email,
                $user->password,
                $user->role,
                $user->status,
                $user->reset_token,
                $user->reset_expiry,
                $user->created_at,
                $user->updated_at,
            ]);
        
            $user->id = $this->connection->lastInsertId();
            return $user;
        } catch (PDOException $err) {
            error_log("Error saat menyimpan user: " . $err->getMessage());
            return $user;
        }
    }

    public function findById(string $username): ?User
    {
        try {
            $statement = $this->connection->prepare("SELECT * FROM users WHERE id = ?");
            $statement->execute([$username]);

            if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                return $this->mapRowToData($row);
            }

            return null;
        } catch (PDOException $err) {
            error_log("Error saat mencari user: " . $err->getMessage());
            return null;
        }
    }
 
    public function update(User $user): User
    {
        try {
            $statement = $this->connection->prepare("
                UPDATE users 
                SET nama = ?, email = ?, password = ?, role = ?, status = ?, updated_at = ?
                WHERE username = ?
            ");

            $statement->execute([
                $user->nama,
                $user->email,
                $user->password,
                $user->role,
                $user->status,
                $user->updated_at,
                $user->username,
            ]);

            return $user;
        } catch (PDOException $err) {
            error_log("Error saat memperbarui user: " . $err->getMessage());
            return $user;
        }
    }

    public function deletePermanently(string $username): bool
    {
        try {
            $statement = $this->connection->prepare("DELETE FROM users WHERE username = ?");
            return $statement->execute([$username]);
        } catch (PDOException $err) {
            error_log("Error saat menghapus data secara permanen: " . $err->getMessage());
            return false;
        }
    }

    public function deleteAllPermanently(): bool
    {
        try {
            $statement = $this->connection->prepare("DELETE FROM users WHERE 1");
            return $statement->execute();
        } catch (PDOException $err) {
            error_log("Error saat menghapus semua data secara permanen: " . $err->getMessage());
            return false;
        }
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ? $this->mapRowToData($stmt->fetch()) : null;
    }

    public function findByResetToken(string $token): ?User
    {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE reset_token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch() ? $this->mapRowToData($stmt->fetch()) : null;
    }

    public function count(string $condition = '', array $params = []): int
    {
        $query = "SELECT COUNT(*) FROM users";
        if ($condition) {
            $query .= " WHERE " . $condition;
        }
        
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }
}

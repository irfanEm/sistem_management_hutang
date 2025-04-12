<?php

namespace IRFANM\SIASHAF\Repository;

use IRFANM\SIASHAF\Domain\User;
use IRFANM\SIASHAF\Helper\SoftDeletes;
use PDO;
use PDOException;

class UserRepository
{
    use SoftDeletes;
    private PDO $connection;
    private const TABLE_NAME = 'users';

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    private function mapRowToData(array $row): User
    {
        $user = new User();
        $user->user_id = $row['user_id'];
        $user->name = $row['name'];
        $user->username = $row['username'];
        $user->password = $row['password'];
        $user->role = $row['role'];
        $user->created_at = $row['created_at'];
        $user->updated_at = $row['updated_at'];
        $user->deleted_at = $row['deleted_at'];

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
                INSERT INTO users (user_id, name, username, password, role, created_at, updated_at, deleted_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $statement->execute([
                $user->user_id,
                $user->name,
                $user->username,
                $user->password,
                $user->role,
                $user->created_at,
                $user->updated_at,
                $user->deleted_at,
            ]);

            return $user;
        } catch (PDOException $err) {
            error_log("Error saat menyimpan user: " . $err->getMessage());
            return $user;
        }
    }

    public function update(User $user): User
    {
        try {
            $statement = $this->connection->prepare("
                UPDATE users 
                SET name = ?, username = ?, password = ?, role = ?, created_at = ?, updated_at = ?, deleted_at = ?
                WHERE user_id = ?
            ");

            $statement->execute([
                $user->name,
                $user->username,
                $user->password,
                $user->role,
                $user->created_at,
                $user->updated_at,
                $user->deleted_at,
                $user->user_id,
            ]);

            return $user;
        } catch (PDOException $err) {
            error_log("Error saat memperbarui user: " . $err->getMessage());
            return $user;
        }
    }

    public function findById(string $user_id): ?User
    {
        try {
            $statement = $this->connection->prepare("SELECT * FROM users WHERE user_id = ? AND deleted_at IS NULL");
            $statement->execute([$user_id]);

            if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                return $this->mapRowToData($row);
            }

            return null;
        } catch (PDOException $err) {
            error_log("Error saat mencari user: " . $err->getMessage());
            return null;
        }
    }
    
    public function findByUsername(string $username): ?User
    {
        try {
            $statement = $this->connection->prepare("SELECT * FROM users WHERE username = ? AND deleted_at IS NULL");
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

    public function deletePermanently(string $user_id): bool
    {
        try {
            $statement = $this->connection->prepare("DELETE FROM users WHERE user_id = ?");
            return $statement->execute([$user_id]);
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
}

<?php

namespace IRFANM\SIASHAF\Repository;

use IRFANM\SIASHAF\Domain\ClassDomain;
use IRFANM\SIASHAF\Helper\SoftDeletes;
use PDO;
use PDOException;

class ClassRepository
{
    use SoftDeletes;
    private PDO $connection;
    private const TABLE_NAME = 'classes';

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    private function mapRowToData(array $row): ClassDomain
    {
        $class = new ClassDomain();
        $class->class_id = $row['class_id'];
        $class->name = $row['name'];
        $class->description = $row['description'] ?? null;
        $class->created_at = $row['created_at'];
        $class->updated_at = $row['updated_at'];
        $class->deleted_at = $row['deleted_at'] ?? null;

        return $class;
    }

    public function save(ClassDomain $class): ClassDomain
    {
        try {
            $statement = $this->connection->prepare("
                INSERT INTO " . self::TABLE_NAME . " (class_id, name, description, created_at, updated_at, deleted_at)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $statement->execute([
                $class->class_id,
                $class->name,
                $class->description,
                $class->created_at,
                $class->updated_at,
                $class->deleted_at,
            ]);

            return $class;
        } catch (PDOException $err) {
            error_log("Error saat menyimpan kelas: " . $err->getMessage());
            return $class;
        }
    }

    public function update(ClassDomain $class): ClassDomain
    {
        try {
            $statement = $this->connection->prepare("
                UPDATE " . self::TABLE_NAME . " 
                SET name = ?, description = ?, created_at = ?, updated_at = ?, deleted_at = ?
                WHERE class_id = ?
            ");

            $statement->execute([
                $class->name,
                $class->description,
                $class->created_at,
                $class->updated_at,
                $class->deleted_at,
                $class->class_id,
            ]);

            return $class;
        } catch (PDOException $err) {
            error_log("Error saat memperbarui kelas: " . $err->getMessage());
            return $class;
        }
    }

    public function findByClassId(string $class_id): ?ClassDomain
    {
        try {
            $statement = $this->connection->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE class_id = ? AND deleted_at IS NULL");
            $statement->execute([$class_id]);

            if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                return $this->mapRowToData($row);
            }

            return null;
        } catch (PDOException $err) {
            error_log("Error saat mencari kelas: " . $err->getMessage());
            return null;
        }
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
        } catch (PDOException $err) {
            error_log("Error saat mengambil data kelas: " . $err->getMessage());
            return [];
        }
    }

    public function deletePermanently(string $class_id): bool
    {
        try {
            $statement = $this->connection->prepare("DELETE FROM " . self::TABLE_NAME . " WHERE class_id = ?");
            return $statement->execute([$class_id]);
        } catch (PDOException $err) {
            error_log("Error saat menghapus kelas secara permanen: " . $err->getMessage());
            return false;
        }
    }

    public function deleteAllPermanently(): bool
    {
        try {
            $statement = $this->connection->prepare("DELETE FROM classes WHERE 1");
            return $statement->execute();
        } catch (PDOException $err) {
            error_log("Error saat menghapus semua data secara permanen: " . $err->getMessage());
            return false;
        }
    }
}

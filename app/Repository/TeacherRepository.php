<?php

namespace IRFANM\SIASHAF\Repository;

use IRFANM\SIASHAF\Domain\Teacher;
use IRFANM\SIASHAF\Helper\SoftDeletes;
use PDO;
use PDOException;

class TeacherRepository
{
    use SoftDeletes;
    
    private PDO $connection;
    private const TABLE_NAME = 'teachers';

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    private function mapRowToData(array $row): Teacher
    {
        $teacher = new Teacher();
        $teacher->user_id = $row['user_id'];
        $teacher->teacher_code = $row['teacher_code'];
        $teacher->first_name = $row['first_name'];
        $teacher->last_name = $row['last_name'];
        $teacher->email = $row['email'];
        $teacher->phone = $row['phone'] ?? null;
        $teacher->address = $row['address'] ?? null;
        $teacher->date_of_birth = $row['date_of_birth'] ?? null;
        $teacher->hire_date = $row['hire_date'] ?? null;
        $teacher->department = $row['department'] ?? null;
        $teacher->status = $row['status'];
        $teacher->created_at = $row['created_at'];
        $teacher->updated_at = $row['updated_at'];
        $teacher->deleted_at = $row['deleted_at'] ?? null;

        return $teacher;
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

    public function save(Teacher $teacher): Teacher
    {
        try {
            $statement = $this->connection->prepare("
                INSERT INTO teachers (user_id, teacher_code, first_name, last_name, email, phone, address, date_of_birth, hire_date, department, status, created_at, updated_at, deleted_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $statement->execute([
                $teacher->user_id,
                $teacher->teacher_code,
                $teacher->first_name,
                $teacher->last_name,
                $teacher->email,
                $teacher->phone,
                $teacher->address,
                $teacher->date_of_birth,
                $teacher->hire_date,
                $teacher->department,
                $teacher->status,
                $teacher->created_at,
                $teacher->updated_at,
                $teacher->deleted_at,
            ]);

            return $teacher;
        } catch (PDOException $err) {
            error_log("Error saat menyimpan teacher: " . $err->getMessage());
            return $teacher;
        }
    }

    public function update(Teacher $teacher): Teacher
    {
        try {
            $statement = $this->connection->prepare("
                UPDATE teachers 
                SET teacher_code = ?, first_name = ?, last_name = ?, email = ?, phone = ?, address = ?, date_of_birth = ?, hire_date = ?, department = ?, status = ?, created_at = ?, updated_at = ?, deleted_at = ?
                WHERE user_id = ?
            ");

            $statement->execute([
                $teacher->teacher_code,
                $teacher->first_name,
                $teacher->last_name,
                $teacher->email,
                $teacher->phone,
                $teacher->address,
                $teacher->date_of_birth,
                $teacher->hire_date,
                $teacher->department,
                $teacher->status,
                $teacher->created_at,
                $teacher->updated_at,
                $teacher->deleted_at,
                $teacher->user_id,
            ]);

            return $teacher;
        } catch (PDOException $err) {
            error_log("Error saat memperbarui teacher: " . $err->getMessage());
            return $teacher;
        }
    }

    public function findByUserId(string $user_id): ?Teacher
    {
        try {
            $statement = $this->connection->prepare("SELECT * FROM teachers WHERE user_id = ? AND deleted_at IS NULL");
            $statement->execute([$user_id]);

            if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                return $this->mapRowToData($row);
            }

            return null;
        } catch (PDOException $err) {
            error_log("Error saat mencari teacher: " . $err->getMessage());
            return null;
        }
    }

    public function deletePermanently(string $user_id): bool
    {
        try {
            $statement = $this->connection->prepare("DELETE FROM teachers WHERE user_id = ?");
            return $statement->execute([$user_id]);
        } catch (PDOException $err) {
            error_log("Error saat menghapus data secara permanen: " . $err->getMessage());
            return false;
        }
    }

    public function deleteAllPermanently(): bool
    {
        try {
            $statement = $this->connection->prepare("DELETE FROM teachers WHERE 1");
            return $statement->execute();
        } catch (PDOException $err) {
            error_log("Error saat menghapus semua data secara permanen: " . $err->getMessage());
            return false;
        }
    }
}

<?php

namespace IRFANM\SIASHAF\Repository;

use IRFANM\SIASHAF\Domain\Student;
use IRFANM\SIASHAF\Helper\SoftDeletes;
use PDO;
use PDOException;

class StudentRepository
{
    use SoftDeletes;

    private PDO $connection;
    private const TABLE_NAME = 'students';

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    private function mapRowToData(array $row): Student
    {
        $student = new Student();
        $student->id = $row['id'];
        $student->user_id = $row['user_id'];
        $student->student_code = $row['student_code'];
        $student->first_name = $row['first_name'];
        $student->last_name = $row['last_name'];
        $student->email = $row['email'] ?? null;
        $student->phone = $row['phone'] ?? null;
        $student->address = $row['address'] ?? null;
        $student->date_of_birth = $row['date_of_birth'] ?? null;
        $student->class_id = $row['class_id'] ?? null;
        $student->enrollment_date = $row['enrollment_date'];
        $student->status = $row['status'];
        $student->created_at = $row['created_at'];
        $student->updated_at = $row['updated_at'];
        $student->deleted_at = $row['deleted_at'] ?? null;

        return $student;
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
            error_log("Query: $query");
            throw new \Exception("Error saat mengambil data " . self::TABLE_NAME . ": " . $err->getMessage());
        }
    }

    public function save(Student $student): Student
    {
        try {
            $statement = $this->connection->prepare("
                INSERT INTO students (
                    user_id, student_code, first_name, last_name, email, phone, address, 
                    date_of_birth, class_id, enrollment_date, status, created_at, updated_at, deleted_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $statement->execute([
                $student->user_id,
                $student->student_code,
                $student->first_name,
                $student->last_name,
                $student->email,
                $student->phone,
                $student->address,
                $student->date_of_birth,
                $student->class_id,
                $student->enrollment_date,
                $student->status,
                $student->created_at,
                $student->updated_at,
                $student->deleted_at,
            ]);

            return $student;
        } catch (PDOException $err) {
            error_log("Error saat menyimpan student: " . $err->getMessage());
            return $student;
        }
    }

    public function update(Student $student): Student
    {
        try {
            $statement = $this->connection->prepare("
                UPDATE students 
                SET 
                    student_code = ?, first_name = ?, last_name = ?, email = ?, 
                    phone = ?, address = ?, date_of_birth = ?, class_id = ?, 
                    enrollment_date = ?, status = ?, updated_at = ?, deleted_at = ?
                WHERE user_id = ?
            ");

            $statement->execute([
                $student->student_code,
                $student->first_name,
                $student->last_name,
                $student->email,
                $student->phone,
                $student->address,
                $student->date_of_birth,
                $student->class_id,
                $student->enrollment_date,
                $student->status,
                $student->updated_at,
                $student->deleted_at,
                $student->user_id,
            ]);

            return $student;
        } catch (PDOException $err) {
            error_log("Error saat memperbarui student: " . $err->getMessage());
            return $student;
        }
    }

    public function findByUserId(string $user_id): ?Student
    {
        try {
            $statement = $this->connection->prepare("SELECT * FROM students WHERE user_id = ? AND deleted_at IS NULL");
            $statement->execute([$user_id]);

            if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                return $this->mapRowToData($row);
            }

            return null;
        } catch (PDOException $err) {
            error_log("Error saat mencari student: " . $err->getMessage());
            return null;
        }
    }

    public function deletePermanently(string $user_id): bool
    {
        try {
            $statement = $this->connection->prepare("DELETE FROM students WHERE user_id = ?");
            return $statement->execute([$user_id]);
        } catch (PDOException $err) {
            error_log("Error saat menghapus student secara permanen: " . $err->getMessage());
            return false;
        }
    }

    public function deleteAllPermanently(): bool
    {
        try {
            $statement = $this->connection->prepare("DELETE FROM students WHERE 1");
            return $statement->execute();
        } catch (PDOException $err) {
            error_log("Error saat menghapus semua data student secara permanen: " . $err->getMessage());
            return false;
        }
    }
}
